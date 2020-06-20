<?php
//ngasih warna
$kecamatan = [
	"Bajuin"=>"#ff0000",
	"Bati_Bati"=>"#00ff00", 
	"Batu_Ampar"=>"#ffff00", 
	"Bumi_Makmur"=>"#ff0000",
	"Jorong"=>"#00ff00",
	"Kintap"=>"#ffff00",
	"Kurau"=>"#00ff00",
	"Panyipatan"=>"#00ff00",
	"Pelaihari"=>"#00ff00",
	"Takisung"=>"#00ff00",
	"Tambang_Ulang"=>"#ffff00"
			];

?>
<!DOCTYPE html>
<html>
<head>
	<title>WebGIS GeoJson</title>

   <!--manggil drawing -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>


	 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin=""/>
   
   <!--manggil legend -->
   <link rel="stylesheet" href="assets/js/leaflet-panel-layers-master/src/leaflet-panel-layers.css" />
   <style type="text/css">
   	#mapid { height: 100vh; }
   	.icon {
	display: inline-block;
	margin: 2px;
	height: 16px;
	width: 16px;
	background-color: #ccc;
}
.icon-bar {
	background: url('assets/js/leaflet-panel-layers-master/examples/images/icons/bar.png') center center no-repeat;
}
   </style>
</head>
<body>
 <CENTER><h1>ZONA Covid-19 Kota Demak</h1></CENTER>
  <div class="card" id="mapid"></div>
</body>
 <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js" integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
   crossorigin=""></script>

 <script src="assets/js/leaflet-panel-layers-master/src/leaflet-panel-layers.js"></script>
 <script src="assets/js/leaflet.ajax.js"></script>

   <script type="text/javascript">

   	var mymap = L.map('mapid').setView([-6.894460, 110.637649], 10); // lokasi demak

   	var LayerKita=L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>, <a href="http://osm.org/copyright">OpenStreetMap</a> contributors,',

	maxZoom: 18,
    id: 'mapbox.streets',
    accessToken: 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw'
});

	mymap.addLayer(LayerKita);






//pop up yang muncul
	function popUp(f,l){
	    var out = [];
	    if (f.properties){
	        // for(key in f.properties){
	        // 	console.log(key);

	        // }
			out.push("ZONA: "+f.properties['PROVINSI']);
			out.push("Kecamatan: "+f.properties['KECAMATAN']);
			out.push("ODP: "+f.properties['DESA']);
			out.push("PDP: "+f.properties['SUMBER']);
			out.push("Kematian: "+f.properties['KODE2010']);
			out.push("ulul dan Fajrul");
	        l.bindPopup(out.join("<br />"));
	    }
	}


	// legend

	function iconByName(name) {
		return '<i class="icon" style="background-color:'+name+';border-radius:50%"></i>';
	}

	function featureToMarker(feature, latlng) {
		return L.marker(latlng, {
			icon: L.divIcon({
				className: 'marker-'+feature.properties.amenity,
				html: iconByName(feature.properties.amenity),
				iconUrl: '../images/markers/'+feature.properties.amenity+'.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			})
		});
	}

	
//jenis tampilan map
	var baseLayers = [
		{
			name: "OpenStreetMap",
			layer: LayerKita
		},
		{	
			name: "OpenCycleMap",
			layer: L.tileLayer('http://{s}.tile.opencyclemap.org/cycle/{z}/{x}/{y}.png')
		},
		{
			name: "Outdoors",
			layer: L.tileLayer('http://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png')
		}
	];

	
//manggil file2 geojson

	<?php
		foreach ($kecamatan as $key => $value) {
			?>

			var myStyle<?=$key?> = {
			    "color": "<?=$value?>",
			    "weight": 1,
			    "opacity": 1
			};

			<?php
			$arrayKec[]='{
			name: "'.str_replace('_', ' ', $key).'",
			icon: iconByName("'.$value.'"),
			layer: new L.GeoJSON.AJAX(["assets/geojson/'.$key.'.geojson"],{onEachFeature:popUp,style: myStyle'.$key.',pointToLayer: featureToMarker }).addTo(mymap)
			}';

			
		}
	?>

	var overLayers = [{
		group: "Layer Kecamatan",
		layers: [
			<?=implode(',', $arrayKec);?>
		]
	}
	];

	var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers,{
		collapsibleGroups: true
	});

	mymap.addControl(panelLayers);

	var drawnItems = new L.FeatureGroup();
     mymap.addLayer(drawnItems);
     var drawControl = new L.Control.Draw({
         edit: {
             featureGroup: drawnItems
         }
     });
     mymap.addControl(drawControl);


   </script>
</html>