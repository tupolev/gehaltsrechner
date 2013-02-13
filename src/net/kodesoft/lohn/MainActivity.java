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
import android.os.Handler;
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
		//response_map = new HashMap<String,String>();
		res = getResources();
		
		setContentView(R.layout.activity_main);
	}
	
	public void onClickBerechnenButton(View view) {
		
			
					
			Intent i = new Intent(this, ResultActivity.class);	        
	        collectRequestParams(i);
//			i.putExtra("brutto", response_map.get("brutto"));
//	        i.putExtra("lohnsteuer", response_map.get("lohnsteuer"));
//	        i.putExtra("soliz", response_map.get("soliz"));
//	        i.putExtra("kirchenst", response_map.get("kirchenst"));
//	        i.putExtra("kv", response_map.get("kv"));
//	        i.putExtra("pv", response_map.get("pv"));
//	        i.putExtra("rv", response_map.get("rv"));
//	        i.putExtra("av", response_map.get("av"));
//	        i.putExtra("netto", response_map.get("netto"));
	        //i.putExtra("", );
	        startActivity(i);
			
	        
		
	}
	
	private void collectRequestParams(Intent i) {
		Spinner lv = (Spinner)findViewById(R.id.birthDateSpinner);
		String[] item_values = res.getStringArray(R.array.birth_date_values);
		//request_params.add(new BasicNameValuePair("ajahr", item_values[lv.getSelectedItemPosition()]));
		i.putExtra("ajahr", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.lstklasse_values);
		lv = (Spinner)findViewById(R.id.lstKlasseSpinner);
		//request_params.add(new BasicNameValuePair("stkl", item_values[lv.getSelectedItemPosition()]));
		i.putExtra("stkl", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.kinderfrei_values);
		lv = (Spinner)findViewById(R.id.kinderfreiSpinner);
		//request_params.add(new BasicNameValuePair("zkf", item_values[lv.getSelectedItemPosition()]));
		i.putExtra("zkf", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.bundesland_values);
		lv = (Spinner)findViewById(R.id.bundeslandSpinner);
		//request_params.add(new BasicNameValuePair("bundesland", item_values[lv.getSelectedItemPosition()]));
		i.putExtra("bundesland", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.kirchensteuer_values);
		lv = (Spinner)findViewById(R.id.kirchensteuerSpinner);
		//request_params.add(new BasicNameValuePair("r", item_values[lv.getSelectedItemPosition()]));
		i.putExtra("r", item_values[lv.getSelectedItemPosition()]);
		
		RadioGroup rg = (RadioGroup)findViewById(R.id.rvPflichtigRadioGroup); 
		int selectedId = rg.getCheckedRadioButtonId();
		RadioButton rb = (RadioButton)findViewById(selectedId);
		//request_params.add(new BasicNameValuePair("e_krv", rb.getTag().toString()));
		i.putExtra("e_krv", rb.getTag().toString());
		
		rg = (RadioGroup)findViewById(R.id.under23RadioGroup); 
		selectedId = rg.getCheckedRadioButtonId();
		rb = (RadioButton)findViewById(selectedId);
		//request_params.add(new BasicNameValuePair("kinderlos", rb.getTag().toString()));
		i.putExtra("kinderlos", rb.getTag().toString());
		
		EditText et = (EditText)findViewById(R.id.kvBeiEditText);
		//request_params.add(new BasicNameValuePair("kvsatz", et.getText().toString()));
		i.putExtra("kvsatz", et.getText().toString());
		
		item_values = res.getStringArray(R.array.zeitraum_values);
		lv = (Spinner)findViewById(R.id.zeitraumSpinner);
		//request_params.add(new BasicNameValuePair("lzz", item_values[lv.getSelectedItemPosition()]));
		i.putExtra("lzz", item_values[lv.getSelectedItemPosition()]);
		
		et = (EditText)findViewById(R.id.bruttoEditText);
		//request_params.add(new BasicNameValuePair("re4", et.getText().toString()));
		i.putExtra("re4", et.getText().toString());
	
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
