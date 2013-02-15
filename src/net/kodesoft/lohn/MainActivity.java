package net.kodesoft.lohn;

import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.message.BasicNameValuePair;

import android.content.Intent;
import android.content.res.Resources;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
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
		res = getResources();
		
		setContentView(R.layout.activity_main);
	}
	
	public void onClickBerechnenButton(View view) {
		
			
					
			Intent i = new Intent(this, ResultActivity.class);	        
	        collectRequestParams(i);
	        startActivity(i);
			
	        
		
	}
	
	private void collectRequestParams(Intent i) {
		Spinner lv = (Spinner)findViewById(R.id.birthDateSpinner);
		String[] item_values = res.getStringArray(R.array.birth_date_values);
		i.putExtra("ajahr", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.lstklasse_values);
		lv = (Spinner)findViewById(R.id.lstKlasseSpinner);
		i.putExtra("stkl", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.kinderfrei_values);
		lv = (Spinner)findViewById(R.id.kinderfreiSpinner);
		i.putExtra("zkf", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.bundesland_values);
		lv = (Spinner)findViewById(R.id.bundeslandSpinner);
		i.putExtra("bundesland", item_values[lv.getSelectedItemPosition()]);
		
		item_values = res.getStringArray(R.array.kirchensteuer_values);
		lv = (Spinner)findViewById(R.id.kirchensteuerSpinner);
		i.putExtra("r", item_values[lv.getSelectedItemPosition()]);
		
		RadioGroup rg = (RadioGroup)findViewById(R.id.rvPflichtigRadioGroup); 
		int selectedId = rg.getCheckedRadioButtonId();
		RadioButton rb = (RadioButton)findViewById(selectedId);
		i.putExtra("e_krv", rb.getTag().toString());
		
		rg = (RadioGroup)findViewById(R.id.under23RadioGroup); 
		selectedId = rg.getCheckedRadioButtonId();
		rb = (RadioButton)findViewById(selectedId);
		i.putExtra("kinderlos", rb.getTag().toString());
		
		EditText et = (EditText)findViewById(R.id.kvBeiEditText);
		i.putExtra("kvsatz", et.getText().toString());
		
		item_values = res.getStringArray(R.array.zeitraum_values);
		lv = (Spinner)findViewById(R.id.zeitraumSpinner);
		i.putExtra("lzz", item_values[lv.getSelectedItemPosition()]);
		
		et = (EditText)findViewById(R.id.bruttoEditText);
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
