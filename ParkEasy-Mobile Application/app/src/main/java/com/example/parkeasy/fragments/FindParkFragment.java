package com.example.parkeasy.fragments;

import android.Manifest;
import android.app.Dialog;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.Resources;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.app.ActivityCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.RecyclerView;


import com.example.parkeasy.R;
import com.example.parkeasy.activites.ParkListItemActivity;
import com.example.parkeasy.adapters.ParkListAdapter;
import com.example.parkeasy.api.RetrofitClient;
import com.example.parkeasy.models.ParkList;
import com.example.parkeasy.models.ParkListResponse;
import com.example.parkeasy.models.ParkOwnerPhnCap;
import com.example.parkeasy.models.ParkOwnerPhnCapResponse;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MapStyleOptions;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import static androidx.coordinatorlayout.widget.CoordinatorLayout.Behavior.setTag;

public class FindParkFragment extends Fragment implements OnMapReadyCallback {

    private GoogleMap mMap;
    private Dialog myDialog;
    private List<ParkList> parkList;
    private ParkListAdapter parkListAdapter;
    private RecyclerView recyclerView;




    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View v = inflater.inflate( R.layout.fragment_find_park, container, false );
        myDialog = new Dialog(getContext());
        return v;
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        SupportMapFragment mapFragment = (SupportMapFragment) getChildFragmentManager().findFragmentById( R.id.map );
        mapFragment.getMapAsync( this );
        //User user = SharedPrefManager.getInstance(getActivity()).getUser();

    }




    @Override
    public void onMapReady(GoogleMap googleMap) {

        mMap = googleMap;
        try {
            boolean success = googleMap.setMapStyle( MapStyleOptions.loadRawResourceStyle( getContext(),R.raw.mapstyler_stander ) );
            if (!success){
                Log.e("Find Park","Style map failed");
            }
        }catch (Resources.NotFoundException e){
            Log.e("Find Park", "Can't find MAP_Style"+e);
        }

        LatLng dhaka = new LatLng( 23.7632926,90.4110731 ); //find uyour place lat and long
        mMap.moveCamera( CameraUpdateFactory.newLatLngZoom( dhaka, 11 ) );
        mMap.getUiSettings().setZoomControlsEnabled( true );
        final Marker marker = mMap.addMarker( new MarkerOptions().position( dhaka ) );
        marker.remove();
        if (ActivityCompat.checkSelfPermission( getContext(), Manifest.permission.ACCESS_FINE_LOCATION ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(getActivity().getApplicationContext(), Manifest.permission.ACCESS_COARSE_LOCATION ) != PackageManager.PERMISSION_GRANTED) {
            return;
        }
        mMap.setMyLocationEnabled( true );
        mMap.getUiSettings().setMyLocationButtonEnabled(true);

       Call<ParkListResponse> call = RetrofitClient.getInstance().getApi().getParkList();
       call.enqueue(new Callback<ParkListResponse>() {
           @Override
           public void onResponse(Call<ParkListResponse> call, Response<ParkListResponse> response) {
               ParkListResponse listResponse = response.body();
               List<ParkList> parkList = listResponse.getParkList();
               for(int i=0 ; i<parkList.size() ; i++){
                   ParkList object = parkList.get(i);
                   showMarker(new LatLng(object.getLatitude(), object.getLongitude()), object.getPark_name(), object.getPhone(),object.getAddress(), object.getCapacity());
               }

               //Arraylist<ParkListResponse> model = new ArrayList<ParkListResponse>(Arrays.asList(response));


           }

           @Override
           public void onFailure(Call<ParkListResponse> call, Throwable t) {

           }
       });




       // showMarker(new LatLng(23.743757, 90.38459), "DIU Parking Interface", "01700000000","h", "6");
    }


    private void showMarker(LatLng point, final String prknm, final String num, final String add, final String cap ) {

        final MarkerOptions markerOptions =  new MarkerOptions();
        markerOptions.position( point );
        mMap.addMarker(markerOptions.icon( BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_MAGENTA) ) );

        mMap.addMarker( markerOptions.title(prknm).snippet(num) );

        markerOptions.draggable( true );


        mMap.setOnInfoWindowClickListener( new GoogleMap.OnInfoWindowClickListener() {
            @Override
            public void onInfoWindowClick(final Marker marker) {

                Call<ParkOwnerPhnCapResponse> call = RetrofitClient.getInstance().getApi().phnNcapacityByParkNmNAdd_park_owner(marker.getTitle());
                call.enqueue(new Callback<ParkOwnerPhnCapResponse>() {
                    @Override
                    public void onResponse(Call<ParkOwnerPhnCapResponse> call, Response<ParkOwnerPhnCapResponse> response) {
                        ParkOwnerPhnCapResponse parkOwnerPhnCapResponse = response.body();
                        ParkOwnerPhnCap parkOwnerPhnCap = parkOwnerPhnCapResponse.getResult();
                        if (parkOwnerPhnCapResponse.isError() == false){
                            Intent i = new Intent(getContext(), ParkListItemActivity.class);
                            i.putExtra( "parkName", marker.getTitle() );
                            i.putExtra( "add", marker.getSnippet());
                            i.putExtra( "phn",  parkOwnerPhnCap.getPhone() );
                            i.putExtra( "cap", parkOwnerPhnCap.getCapacity() );
                            startActivity( i );
                        }
                    }

                    @Override
                    public void onFailure(Call<ParkOwnerPhnCapResponse> call, Throwable t) {

                    }
                });






            }
        } );

    }




}
