package net.kodesoft.lohn;

import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashMap;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;

import android.app.Activity;
import android.os.Bundle;
import android.app.ActionBar;
import android.app.AlertDialog;
import android.app.FragmentTransaction;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.NavUtils;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import android.content.*;
import android.content.res.Resources;

public class ResultActivity extends Activity {

	public ArrayList<BasicNameValuePair> request_params;
	public HashMap<String,String> response_map;
	public Resources res;
	
	
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.result_layout);
        request_params = new ArrayList<BasicNameValuePair>(2);
		response_map = new HashMap<String,String>();		
        Bundle extras = getIntent().getExtras();
        request_params.add(new BasicNameValuePair("ajahr", extras.getString("ajahr")));
        request_params.add(new BasicNameValuePair("stkl", extras.getString("stkl")));
        request_params.add(new BasicNameValuePair("zkf", extras.getString("zkf")));
        request_params.add(new BasicNameValuePair("bundesland", extras.getString("bundesland")));
        request_params.add(new BasicNameValuePair("r", extras.getString("r")));
        request_params.add(new BasicNameValuePair("e_krv", extras.getString("e_krv")));
        request_params.add(new BasicNameValuePair("kinderlos", extras.getString("kinderlos")));
        request_params.add(new BasicNameValuePair("lzz", extras.getString("lzz")));
        request_params.add(new BasicNameValuePair("re4", extras.getString("re4")));
         
    	new Thread(new Runnable() {
		    public void run() {
		    	try {
					getResultsFromService(request_params);
					((TextView) findViewById(R.id.bruttoTextView)).setText(response_map.get("brutto"));
	    	        ((TextView) findViewById(R.id.lstTextView)).setText(response_map.get("lohnsteuer"));
	    	        ((TextView) findViewById(R.id.solizTextView)).setText(response_map.get("soliz"));
	    	        ((TextView) findViewById(R.id.kirchenstTextView)).setText(response_map.get("kirchenst"));
	    	        ((TextView) findViewById(R.id.kvTextView)).setText(response_map.get("kv"));
	    	        ((TextView) findViewById(R.id.pvTextView)).setText(response_map.get("pv"));
	    	        ((TextView) findViewById(R.id.rvTextView)).setText(response_map.get("rv"));
	    	        ((TextView) findViewById(R.id.avTextView)).setText(response_map.get("av"));
	    	        ((TextView) findViewById(R.id.nettoTextView)).setText(response_map.get("netto"));
				} catch (Exception e) {
					showToast(e.getMessage());
				}    			
		    }
    	}).start();
               
    }
	
	public void backButtonClick(View view) {
		this.finish();
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
}