<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/html");
    require('fx.php');
    
    function getImage($source){
        $imgInfo=getimagesize($source);
        $mime=$imgInfo['mime'];
        #Create a new image from file
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($source);
                break;
            case 'image/png':
                return imagecreatefrompng($source);
                break;
            case 'image/gif':
                return imagecreatefromgif($source);
                break;    
            default:
                return imagecreatefromjpeg($source);
                break;
        }
    }

    function encrypt($source,$message_to_hide){
        $image=getImage($source);
        $bin_message = tobin($message_to_hide);
        $bin_len = strlen($bin_message);

        for ($x = 0; $x < $bin_len ; $x++) { 
            $y = $x;
            $rgb = imagecolorat($image,$x,$y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            $newR = $r;
            $newG = $g;
            $newB = tobin($b);
            $newB[strlen($newB)-1] = $bin_message[$x];
            $newB = toString($newB);
    
            $new_color = imagecolorallocate($image,$newR,$newG,$newB);
            imagesetpixel($image,$x,$y,$new_color);
        }
        $output = "output/output.png";
        imagepng($image,$output);
        imagedestroy($image);
        return ["image"=>$output,"code"=>$x];
    }
    function decrypt($source,$code){
        $image=getImage($source);
        $real_message="";
        if ($code>64) {
            $code=64;
        }
        for ($x = 0; $x < $code; $x++) { 
            $y = $x;
            $rgb = imagecolorat($image,$x,$y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            $blue = tobin($b);
            $real_message =$real_message.$blue[strlen($blue)-1];
        }
        return toString($real_message);
    }

    if (isset($_POST["action"])) {
        if ($_POST["action"]=="encrypt") {
            $resp=encrypt($_FILES['image']['tmp_name'],$_POST["message"]);
            echo "<a href='".$resp["image"]."' class='bg-blue-200 text-black py-2 px-5 mt-5' download >Download</a> <br> code : ".$resp["code"];
        }
        
        if ($_POST["action"]=="decrypt") {
            echo "<p>Message : ".decrypt($_FILES['image']['tmp_name'],$_POST["code"])."</p>";
        }
    }
