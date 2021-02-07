package com.example.parkeasy.activites;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;

import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.TextView;
import android.widget.Toast;

import com.example.parkeasy.R;
import com.example.parkeasy.api.RetrofitClient;
import com.example.parkeasy.fragments.FindParkFragment;
import com.example.parkeasy.fragments.MyProfileFragment;
import com.example.parkeasy.fragments.ParkListFragment;
import com.example.parkeasy.fragments.ParkStatusFragment;
import com.example.parkeasy.fragments.RentYourSpaceFragment;
import com.example.parkeasy.fragments.ReportFragment;
import com.example.parkeasy.models.User;
import com.example.parkeasy.storage.SharedPrefManager;
import com.google.android.material.bottomnavigation.BottomNavigationView;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class HomepageActivity extends AppCompatActivity implements BottomNavigationView.OnNavigationItemSelectedListener {

    private TextView textView;
    private MenuItem menuItem;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_homepage);

       // textView = findViewById(R.id.textView2);
        User user = SharedPrefManager.getInstance(this).getUser();
        //textView.setText(String.valueOf(user.getId())+" "+user.getFullName()+" "+user.getVehicleType());


        BottomNavigationView navigationView = findViewById(R.id.bottom_nav);
        navigationView.setOnNavigationItemSelectedListener(this);

        displayFragment(new FindParkFragment());

    }

    private void displayFragment(Fragment fragment) {
        getSupportFragmentManager()
                .beginTransaction()
                .replace(R.id.fragmentContainer, fragment)
                .commit();
    }

    @Override
    protected void onStart() {
        super.onStart();
        if (!SharedPrefManager.getInstance(this).isLoggedIn()) {
            Intent intent = new Intent(this, LoginActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            startActivity(intent);
        }
    }

    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem item) {
        Fragment fragment = null;
        switch (item.getItemId()){
            case R.id.item_findPark:
                fragment = new FindParkFragment();
                break;
            case R.id.item_parkStatus:
               // load_result();
                fragment = new ParkStatusFragment();
                break;
            case R.id.item_parkingList:
                fragment = new ParkListFragment();
                break;
        }

        if (fragment != null){
            displayFragment(fragment);
        }
        return false;

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.options_menu, menu);

        User user = SharedPrefManager.getInstance(this).getUser();
        menuItem = menu.findItem(R.id.itemMenuTitle);
        menuItem.setTitle(user.getFullName());
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.item12:
                getSupportFragmentManager().beginTransaction().replace(R.id.fragmentContainer,new ReportFragment()).commit();
                return true;

            case R.id.item1:
                getSupportFragmentManager().beginTransaction().replace(R.id.fragmentContainer,new MyProfileFragment()).commit();
                return true;

            case R.id.item2:
                Toast.makeText(this, "Item 2 selected", Toast.LENGTH_SHORT).show();
                return true;

            case R.id.item3:
                getSupportFragmentManager().beginTransaction().replace(R.id.fragmentContainer,new RentYourSpaceFragment()).commit();
                Toast.makeText(this, "Item 3 selected", Toast.LENGTH_SHORT).show();
                return true;

            case R.id.item4:
                log_out();
                Toast.makeText(this, " Item 4 selected", Toast.LENGTH_SHORT).show();
                return true;

            default:
                return super.onOptionsItemSelected(item);
        }
    }

    private void log_out() {
            SharedPrefManager.getInstance(HomepageActivity.this).clear();
            Intent intent = new Intent(HomepageActivity.this, LoginActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            startActivity(intent);
    }




}
