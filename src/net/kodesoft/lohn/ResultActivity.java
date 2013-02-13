package net.kodesoft.lohn;

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

public class ResultActivity extends Activity {

	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.result_layout);
        
        Bundle extras = getIntent().getExtras();
        
        ((TextView) findViewById(R.id.bruttoTextView)).setText(extras.getCharSequence("brutto"));
        ((TextView) findViewById(R.id.lstTextView)).setText(extras.getCharSequence("lohnsteuer"));
        ((TextView) findViewById(R.id.solizTextView)).setText(extras.getCharSequence("soliz"));
        ((TextView) findViewById(R.id.kirchenstTextView)).setText(extras.getCharSequence("kirchenst"));
        ((TextView) findViewById(R.id.kvTextView)).setText(extras.getCharSequence("kv"));
        ((TextView) findViewById(R.id.pvTextView)).setText(extras.getCharSequence("pv"));
        ((TextView) findViewById(R.id.rvTextView)).setText(extras.getCharSequence("rv"));
        ((TextView) findViewById(R.id.avTextView)).setText(extras.getCharSequence("av"));
        ((TextView) findViewById(R.id.nettoTextView)).setText(extras.getCharSequence("netto"));
        
        
    }
	
	public void backButtonClick(View view) {
		this.finishActivity(TRIM_MEMORY_COMPLETE);
	}
}