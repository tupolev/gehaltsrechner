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
import android.content.Context;
import android.content.res.Resources;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

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
					runOnUiThread(new Runnable() {
					     public void run() {
					    		String euro = getString(R.string.glob_euro_symbol);
								//HashMap<String, String> str = new HashMap<String, String>();
								
								/*str.put("brutto", response_map.get("brutto") + euro);
								str.put("lohnsteuer", response_map.get("lohnsteuer") + euro);
								str.put("soliz", response_map.get("soliz") + euro);
								str.put("kirchenst", response_map.get("kirchenst") + euro);
								str.put("kv", response_map.get("kv") + euro);
								str.put("pv", response_map.get("pv") + euro);
								str.put("rv", response_map.get("rv") + euro);
								str.put("av", response_map.get("av") + euro);
								str.put("netto", response_map.get("netto") + euro);*/
								
								((TextView) findViewById(R.id.bruttoTextView)).setText(response_map.get("brutto") + euro);
				    	        ((TextView) findViewById(R.id.lstTextView)).setText(response_map.get("lohnsteuer") + euro);
				    	        ((TextView) findViewById(R.id.solizTextView)).setText(response_map.get("soliz") + euro);
				    	        ((TextView) findViewById(R.id.kirchenstTextView)).setText(response_map.get("kirchensteuer") + euro);
				    	        ((TextView) findViewById(R.id.kvTextView)).setText(response_map.get("kv") + euro);
				    	        ((TextView) findViewById(R.id.pvTextView)).setText(response_map.get("pv") + euro);
				    	        ((TextView) findViewById(R.id.rvTextView)).setText(response_map.get("rv") + euro);
				    	        ((TextView) findViewById(R.id.avTextView)).setText(response_map.get("av") + euro);
				    	        ((TextView) findViewById(R.id.sumSteuerTextView)).setText(response_map.get("total_st") + euro);
				    	        ((TextView) findViewById(R.id.sumSvTextView)).setText(response_map.get("total_sv") + euro);
				    	        ((TextView) findViewById(R.id.nettoTextView)).setText(response_map.get("netto") + euro);

					    }
					});
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
	        response_map.put("zeitraum", getNodeContent("zeitraum", doc));	        	        
	        response_map.put("brutto", getNodeContent("brutto", doc));
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