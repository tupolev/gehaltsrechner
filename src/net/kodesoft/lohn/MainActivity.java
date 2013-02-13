package net.kodesoft.lohn;

import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.ParseException;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;

import android.app.ActionBar;
import android.app.AlertDialog;
import android.app.FragmentTransaction;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.NavUtils;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import android.content.*;
import android.content.res.Resources;
public class MainActivity extends FragmentActivity {

	/**
	 * The serialization (saved instance state) Bundle key representing the
	 * current tab position.
	 */
	private static final String STATE_SELECTED_NAVIGATION_ITEM = "selected_navigation_item";
	
	public ArrayList<BasicNameValuePair> request_params;
	public HashMap<String,String> response_map;
	public Resources res;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		request_params = new ArrayList<BasicNameValuePair>(2);
		response_map = new HashMap<String,String>();
		res = getResources();
		

		
		
		
		
		
		
		
		
		
		setContentView(R.layout.activity_main);
	}
	
	public void onClickBerechnenButton(View view) {
		try {
			collectRequestParams();
			getResultsFromService(request_params);
			Intent i = new Intent(this, ResultActivity.class);	        
	        i.putExtra("brutto", response_map.get("brutto"));
	        i.putExtra("lohnsteuer", response_map.get("lohnsteuer"));
	        i.putExtra("soliz", response_map.get("soliz"));
	        i.putExtra("kirchenst", response_map.get("kirchenst"));
	        i.putExtra("kv", response_map.get("kv"));
	        i.putExtra("pv", response_map.get("pv"));
	        i.putExtra("rv", response_map.get("rv"));
	        i.putExtra("av", response_map.get("av"));
	        i.putExtra("netto", response_map.get("netto"));
	        
	        startActivity(i);
	        
		} catch (Exception e) {
			showToast(e.getMessage());
		}
	}

	public void showToast(String message) {
		Context context = getApplicationContext();
		CharSequence text = message;
		int duration = Toast.LENGTH_SHORT;

		Toast toast = Toast.makeText(context, text, duration);
		toast.show();
	}
	
	public String getNodeContent(String nodeName, Document doc) {
		String nodeValue = "";		
		try {
			NodeList nl = doc.getElementsByTagName(nodeName);
	        for(int i = 0; i < nl.getLength(); i++) {
	            if (nl.item(i).getNodeType() == org.w3c.dom.Node.ELEMENT_NODE) {
	                 org.w3c.dom.Element nameElement = (org.w3c.dom.Element) nl.item(i);
	                 nodeValue = nameElement.getFirstChild().getNodeValue().trim();
	             }
	        }
		} catch (Exception e) {
			nodeValue = "";
		}		
		return nodeValue;
	}
	
	public void getResultsFromService(ArrayList<BasicNameValuePair> post) throws Exception {
		try {
			HttpResponse response = postData();
	        HttpEntity r_entity = response.getEntity();
			String xmlString = EntityUtils.toString(r_entity);
        	DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
            DocumentBuilder db = null;    		
			db = factory.newDocumentBuilder();
			InputSource inStream = new InputSource();
	        inStream.setCharacterStream(new StringReader(xmlString));
	        Document doc = null;
	        doc = db.parse(inStream);
	        response_map.put("lohnsteuer", getNodeContent("lohnsteuer", doc));
	        response_map.put("soliz", getNodeContent("soliz", doc));
	        response_map.put("kirchensteuer", getNodeContent("kirchensteuer", doc));
	        response_map.put("kv", getNodeContent("kv", doc));
	        response_map.put("pv", getNodeContent("pv", doc));
	        response_map.put("rv", getNodeContent("rv", doc));
	        response_map.put("av", getNodeContent("av", doc));
	        response_map.put("total_st", getNodeContent("total_st", doc));
	        response_map.put("total_sv", getNodeContent("total_sv", doc));
	        response_map.put("netto", getNodeContent("netto", doc));
		} catch (Exception e) {
			throw e;
		}
	}
	
	private void collectRequestParams() {
		Spinner lv = (Spinner)findViewById(R.id.birthDateSpinner);
		String[] item_values = res.getStringArray(R.array.birth_date_values);
		request_params.add(new BasicNameValuePair("ajahr", item_values[lv.getSelectedItemPosition()]));
		
		
		item_values = res.getStringArray(R.array.lstklasse_values);
		lv = (Spinner)findViewById(R.id.lstKlasseSpinner);
		request_params.add(new BasicNameValuePair("stkl", item_values[lv.getSelectedItemPosition()]));
		
		item_values = res.getStringArray(R.array.kinderfrei_values);
		lv = (Spinner)findViewById(R.id.kinderfreiSpinner);
		request_params.add(new BasicNameValuePair("zkf", item_values[lv.getSelectedItemPosition()]));
		
		item_values = res.getStringArray(R.array.bundesland_values);
		lv = (Spinner)findViewById(R.id.bundeslandSpinner);
		request_params.add(new BasicNameValuePair("bundesland", item_values[lv.getSelectedItemPosition()]));
		
		item_values = res.getStringArray(R.array.kirchensteuer_values);
		lv = (Spinner)findViewById(R.id.kirchensteuerSpinner);
		request_params.add(new BasicNameValuePair("r", item_values[lv.getSelectedItemPosition()]));

		RadioGroup rg = (RadioGroup)findViewById(R.id.rvPflichtigRadioGroup); 
		int selectedId = rg.getCheckedRadioButtonId();
		RadioButton rb = (RadioButton)findViewById(selectedId);
		request_params.add(new BasicNameValuePair("e_krv", rb.getTag().toString()));
		
		rg = (RadioGroup)findViewById(R.id.under23RadioGroup); 
		selectedId = rg.getCheckedRadioButtonId();
		rb = (RadioButton)findViewById(selectedId);
		request_params.add(new BasicNameValuePair("kinderlos", rb.getTag().toString()));
		
		EditText et = (EditText)findViewById(R.id.kvBeiEditText);
		request_params.add(new BasicNameValuePair("kvsatz", et.getText().toString()));
		
		item_values = res.getStringArray(R.array.zeitraum_values);
		lv = (Spinner)findViewById(R.id.zeitraumSpinner);
		request_params.add(new BasicNameValuePair("lzz", item_values[lv.getSelectedItemPosition()]));
		
		et = (EditText)findViewById(R.id.bruttoEditText);
		request_params.add(new BasicNameValuePair("re4", et.getText().toString()));
		
		
		
		/*
		    $_POST['aganzeige']	   =  isset($_POST['aganzeige'])	    ?:  "0" ;
    $_POST['ajahr']        =  isset($_POST['ajahr']     )       ?:  "0" ;
    $_POST['bundesland']   =  isset($_POST['bundesland'])       ?:  "6" ;
    $_POST['e_bg']         =  isset($_POST['e_bg']      )       ?:  "0,8 " ;
    $_POST['e_krv']        =  isset($_POST['e_krv']     )       ?:  "0" ;
    $_POST['e_kvp']        =  isset($_POST['e_kvp']     )       ?:  " " ;
    $_POST['e_pkpv']       =  isset($_POST['e_pkpv']    )       ?:  " " ;
    $_POST['e_pkv']        =  isset($_POST['e_pkv']     )       ?:  "1" ;
    $_POST['e_u1']         =  isset($_POST['e_u1']      )       ?:  "1,1" ;
    $_POST['e_u2']         =  isset($_POST['e_u2']      )       ?:  "0,1" ;
    $_POST['entsch_1']	   =   isset($_POST['entsch_1'])	    ?:  " " ;
    $_POST['entsch_2']	   =  isset($_POST['entsch_2'])	        ?:  " " ;
    $_POST['f']           =  isset($_POST['f']         )        ?:  " " ;
    $_POST['jfreib']      =  isset($_POST['jfreib']    )        ?:  " " ;
    $_POST['jhinzu']      =  isset($_POST['jhinzu']    )        ?:  " " ;
    $_POST['jre4ent']     =  isset($_POST['jre4ent']   )        ?:  " " ;
    $_POST['jsonstb']     =  isset($_POST['jsonstb']   )        ?:  " " ;
    $_POST['kapindex']	   =  isset($_POST['kapindex'])	        ?:  "0" ;
    $_POST['kinderlos']=	    isset($_POST['kinderlos'])	    ?:  "1" ;
    $_POST['kvsatz']   =  isset($_POST['kvsatz']    )           ?:  "15.5" ;
    $_POST['lzz']      =  isset($_POST['lzz']       )           ?:  "2" ;
    $_POST['r']        =  isset($_POST['r']         )           ?:  "0" ;
    $_POST['re4']      =  isset($_POST['re4']       )           ?:  "2500" ;
    $_POST['sonstb']   =  isset($_POST['sonstb']    )           ?: " " ;
    $_POST['sonstent']=	  isset($_POST['sonstent'])	            ?: " " ;
    $_POST['sterbe']  =  isset($_POST['sterbe']    )            ? : " " ;
    $_POST['stkl']    =  isset($_POST['stkl']      )            ? : "1" ;
    $_POST['vbez']    =  isset($_POST['vbez']      )            ? : " " ;
    $_POST['vbezm']   =  isset($_POST['vbezm']     )            ? : " " ;
    $_POST['vbezs']   =  isset($_POST['vbezs']     )            ? : " " ;
    $_POST['vjahr']   =  isset($_POST['vjahr']     )            ? : "2013" ;
    $_POST['vkapa']   =  isset($_POST['vkapa']     )            ? : " " ;
    $_POST['vmt']     =  isset($_POST['vmt']       )            ? : " " ;
    $_POST['zkf']     =  isset($_POST['zkf']       )            ? : "0" ;
    $_POST['zmvb']    =  isset($_POST['zmvb']      )            ? : "12" ;
		*/
	}
	
	private HttpResponse postData() throws Exception {
	    HttpClient httpclient = new DefaultHttpClient();
	    HttpPost httppost = new HttpPost(getString(R.string.url_lohn_service));
	    HttpResponse response;
	    //try {
	    	httppost.setEntity(new UrlEncodedFormEntity(this.request_params));
	    	response = httpclient.execute(httppost);
	    //} catch (Exception e) {
	    //    response = null;	        
	    //    throw new Exception(getString(R.string.err_connecting_server));
	    //}
	    return response;
	} 
	
	
	@Override
	public void onRestoreInstanceState(Bundle savedInstanceState) {
		// Restore the previously serialized current tab position.
		if (savedInstanceState.containsKey(STATE_SELECTED_NAVIGATION_ITEM)) {
			getActionBar().setSelectedNavigationItem(
					savedInstanceState.getInt(STATE_SELECTED_NAVIGATION_ITEM));
		}
	}

	@Override
	public void onSaveInstanceState(Bundle outState) {
		// Serialize the current tab position.
		outState.putInt(STATE_SELECTED_NAVIGATION_ITEM, getActionBar()
				.getSelectedNavigationIndex());
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_main, menu);
		return true;
	}

	/**
	 * A dummy fragment representing a section of the app, but that simply
	 * displays dummy text.
	 */
	public static class DummySectionFragment extends Fragment {
		/**
		 * The fragment argument representing the section number for this
		 * fragment.
		 */
		public static final String ARG_SECTION_NUMBER = "section_number";

		public DummySectionFragment() {
		}

		@Override
		public View onCreateView(LayoutInflater inflater, ViewGroup container,
				Bundle savedInstanceState) {
			// Create a new TextView and set its text to the fragment's section
			// number argument value.
			TextView textView = new TextView(getActivity());
			textView.setGravity(Gravity.CENTER);
			textView.setText(Integer.toString(getArguments().getInt(
					ARG_SECTION_NUMBER)));
			return textView;
		}
	}

}
