<?php

App::uses('Component', 'Controller');
class FileConversionComponent extends Component {
	public function convertObjToStl($srcFile){
		$tempName = round(microtime(true) * 10000) . '_' . md5(String::uuid());
		$tempPovFile = TEMP_FILE_DIR . DS . $tempName . '.stl';
		 
		$str =  "/usr/bin/xvfb-run --auto-servernum /usr/bin/meshlabserver -i " . $srcFile . ' -o ' . $tempPovFile;		
		
		echo "derp \n";
		echo $str;
		echo "\n derp";
		
		$data = shell_exec($str);
		
		return $tempPovFile;
	}
	public function convertStlToPov($srcFile){
		$tempName = round(microtime(true) * 10000) . '_' . md5(String::uuid());
		$tempPovFile = TEMP_FILE_DIR . DS . $tempName . '.pov';
		
		$str =  "/usr/local/bin/stl2pov " . $srcFile . ' > ' . $tempPovFile;
		
		echo "derp \n";
		echo $str;
		echo "\n derp";
		$data = shell_exec($str);
		

		$stlData = file_get_contents($tempPovFile);
		$stlData = preg_replace('/# *declare .*?= *mesh *{/', '#declare m_Default = mesh {', $stlData);
		$stlData.= "\n#declare AspectRatio = " . GALLERY_WIDTH . " / " . GALLERY_HEIGHT . ";\n" . '#include "axes_macro.inc"

global_settings
{
	assumed_gamma 1.0
//	radiosity
//	{
//		brightness 0.3
//	}
}
#declare main_mesh = m_Default;
#declare Camera_location = vrotate( z * 180 , <-63,0, 45> );
#declare Light_location = vrotate(z * 180, <-63, 0, 45>);

#if(clock_on) // Animated circle around mesh
#declare Camera_location = vrotate( z * 180 , <55,0,( frame_number * 360 / final_frame )> );
#end

#declare Light1_location = vrotate( Light_location , z * 20 );
#declare Light2_location = vrotate( Light_location , z * -20 );

background { color rgb 0.98 }

light_source {
	Light1_location 
	rgb 1                 
	translate <-30, 30, 25>
}

light_source {
	Light2_location 
	rgb 1               
	translate <-30, 30, 25>
}

camera {
	perspective
	location Camera_location
	sky z
	//right -4/3*y // Correct to left hand handedness (use this and/or sky z , up z.)
	//up z
	right <AspectRatio, 0, 0> 	
	translate <-30, 30, 30>
	look_at <0,0,10>
}

#declare Min = min_extent( main_mesh );
#declare Max = max_extent( main_mesh );

#declare Center = ( Min + Max ) / 2;
#declare Size = Max - Min;
#declare Scale_Desired = 160 / max( Size.x , Size.y , Size.z * 2 ); // scale to 175 units from which ever dimension is largest
  
Axes_Macro
(
	200,	// Axes_axesSize,	The distance from the origin to one of the grid\'s edges.	(float)
	500/1,	// Axes_majUnit,	The size of each large-unit square.	(float)
	50,	// Axes_minUnit,	The number of small-unit squares that make up a large-unit square.	(integer)
	0.005,	// Axes_thickRatio,	The thickness of the grid lines (as a factor of axesSize).	(float)
	on,	// Axes_aBool,		Turns the axes on/off. (boolian)
	on,	// Axes_mBool,		Turns the minor units on/off. (boolian)
	off,	// Axes_xBool,		Turns the plane perpendicular to the x-axis on/off.	(boolian)
	off,	// Axes_yBool,		Turns the plane perpendicular to the y-axis on/off.	(boolian)
	on	// Axes_zBool,		Turns the plane perpendicular to the z-axis on/off.	(boolian)
)

object
{
	Axes_Object
}

object
{
        main_mesh
        texture
        {
                pigment {color rgb <0.2, 0.55, 1>}
                finish
                {
                        ambient 0.15
                        diffuse 0.85
                        specular 0.3
                }
        }
	translate < -Center.x , -Center.y , -Min.z > // Bottom on z=0 plane, centered x,y
	scale Scale_Desired
}';
		file_put_contents($tempPovFile, $stlData);
		
		return $tempPovFile;
	}
	public function renderPov($srcFile){
		
		$tempName = round(microtime(true) * 10000) . '_' . md5(String::uuid());
		$tempPngFile = TEMP_FILE_DIR . DS . $tempName . '.png';
		
		$renderStr = '/usr/local/bin/povray -s +I"' . $srcFile . '" +FN +W'. GALLERY_WIDTH . ' +H' . GALLERY_HEIGHT . ' +O"' . $tempPngFile . '" +L"/var/www/repables.com/bin/povray" +S +Q11 +AM2 +A2 +A +R2 +MB +UL +UV';// +HS155.90
		echo " \n ";
		echo $renderStr;
		echo "    \n   ";
		$result = shell_exec($renderStr);
		
		echo $result;
		
		return $tempPngFile;
	}
}

?>