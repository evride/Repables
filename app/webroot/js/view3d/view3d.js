(function(window){
	var camera,scene,renderer,material,mesh,directionalLight,controls,stlLoader,renderEnabled;
	var meshes = [];
	function viewModel(filename){
		renderEnabled = false;
		if(!scene){				
			createScene();
		}
		$(renderer.domElement).hide();
		$('#View3DContainer div.progress').show().addClass('progress-striped active');
		var progressBar = $('#View3DContainer div.progress div.bar');
		progressBar.css('width', '5%');
		stlLoader = new THREE.STLLoader();
		stlLoader.addEventListener("progress", function(evt){
			progressBar.css('width', (5 + 95 * (evt.loaded / evt.total)) + "%");
		});
		stlLoader.load(baseURL + filename, fileLoaded);
		
		controls.handleResize();
	};
	function close3dView(){		
		renderEnabled = false;
		if(mesh){
			scene.remove(mesh);
		}
		stlLoader.abort();
	}
	function createScene(){
		scene = new THREE.Scene();

		camera = new THREE.PerspectiveCamera( 70, 878/424, 1, 100000);
		camera.position.z = 70;
		camera.position.y = 0;
		scene.add( camera );
		
		
		

		var ambientLight = new THREE.AmbientLight(0x000B1A);
		scene.add(ambientLight);
		
		directionalLight = new THREE.DirectionalLight( 0xffffff );
		directionalLight.position.x = 0;
		directionalLight.position.y = 0;
		directionalLight.position.z = 1;
		directionalLight.position.normalize();
		directionalLight.intensity = 1.4;
		scene.add( directionalLight );
		
		
		renderer = new THREE.WebGLRenderer(); //new THREE.CanvasRenderer();
		renderer.setSize( 878, 424 );
			
		document.getElementById('View3DContainer').appendChild( renderer.domElement );
		
		controls = new THREE.TrackballControls( camera, document.getElementById('View3DContainer') );

		controls.rotateSpeed = 2.0;
		controls.zoomSpeed = 1.2;

		controls.noZoom = false;
		controls.noPan = false;

		controls.staticMoving = true;
		controls.dynamicDampingFactor = 0.3;
		controls.keys = [ 65, 83, 68 ];
		controls.addEventListener( 'change', controlsChanged );
		
		
		window.scene = scene;
	}

	function controlsChanged(evt){
		render();
	}
	function animate() {
		// note: three.js includes requestAnimationFrame shim
		if(renderEnabled){
			requestAnimationFrame( animate );
		}		
		controls.update();
		directionalLight.position.x = camera.position.x;
		directionalLight.position.y = camera.position.y;
		directionalLight.position.z = camera.position.z;
		//render();

	}
	function render() {
		if (mesh) {
		   // mesh.rotation.z += 0.01;
		}
		renderer.render( scene, camera );
		
	}
	function fileLoaded(geometry){
		console.log(camera, camera.position);
		$('#View3DContainer div.progress').hide();
		$(renderer.domElement).show();
		
		geometry.computeFaceNormals();
		console.log("a");
		THREE.GeometryUtils.center( geometry );
		
		//geometry.applyMatrix( new THREE.Matrix4().translate( new THREE.Vector3(0, 0, 0)));
		
		if(!material){
			material = new THREE.MeshLambertMaterial({
				overdraw:true,
				color: 0x338CFF,
				shading: THREE.SmoothShading
			});
		}
		mesh = new THREE.Mesh(geometry, material);
		
		var scale = 40 / geometry.boundingSphere.radius;
		
		camera.position.set(0, 0, 70);
		camera.rotation.set(0, 0, 0);
		camera.far = 100000;
		camera.near = 1;
		camera.fov = 70;
		camera.up.set(0, 1, 0);
		camera.updateProjectionMatrix();
		
		scene.add(mesh);
		mesh.rotation.x = 5;
		mesh.scale.set(scale, scale, scale);
		mesh.rotation.z = .25;
		renderEnabled = true;
		animate();
		render();
	}
	window.viewModel = viewModel;
	window.close3dView = close3dView;
}(window));