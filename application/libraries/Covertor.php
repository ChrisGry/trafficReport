<?php
class Covertor{

  public static function convertLL2MC($lng,$lat) {
    $LLBAND = array( 75, 60, 45, 30, 15, 0 );
    $LL2MC = array(
            array( -0.0015702102444, 111320.7020616939,
    1704480524535203, -10338987376042340,
    26112667856603880, -35149669176653700,
    26595700718403920, -10725012454188240,
    1800819912950474, 82.5),
            array( 0.0008277824516172526, 111320.7020463578,
    647795574.6671607, -4082003173.641316,
    10774905663.51142, -15171875531.51559,
    12053065338.62167, -5124939663.577472,
    913311935.9512032, 67.5 ),
            array( 0.00337398766765, 111320.7020202162,
    4481351.045890365, -23393751.19931662,
    79682215.47186455, -115964993.2797253,
    97236711.15602145, -43661946.33752821,
    8477230.501135234, 52.5 ),
           array( 0.00220636496208, 111320.7020209128,
    51751.86112841131, 3796837.749470245,
    992013.7397791013, -1221952.21711287,
    1340652.697009075, -620943.6990984312,
    144416.9293806241, 37.5 ),
           array( -0.0003441963504368392, 111320.7020576856,
    278.2353980772752, 2485758.690035394,
    6070.750963243378, 54821.18345352118,
    9540.606633304236, -2710.55326746645,
    1405.483844121726, 22.5 ),
            array( -0.0003218135878613132, 111320.7020701615,
    0.00369383431289, 823725.6402795718,
    0.46104986909093, 2351.343141331292,
    1.58060784298199, 8.77738589078284,
    0.37238884252424, 7.45 ) );

    $result=array();
    $lng = Covertor::getLoop($lng, -180, 180);// 标准化到区间内
    $lat = Covertor::getRange($lat, -74, 74);// 标准化到区间内
    // 查找LLBAND的维度字典，字典由大到小排序，找到则停止
    for ($i = 0; $i < count($LLBAND); $i++) {
        if ($lat >= $LLBAND[$i]) {
            $result= $LL2MC[$i];
            break;
        }
    }
    // 如果没有找到，则反过来找。找到即停止。
    if (!is_null($result)) {
        for ($i = count($LLBAND) - 1; $i >= 0; $i--) {
            if ($lat <= -$LLBAND[$i]) {
                $result= $LL2MC[$i];
                break;
            }
        }
    }
    return  Covertor::convertor($lng,$lat, $result);
  }

    public static function http_get_data($url) {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();

        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        return $return_content;
    }

  public static function convertor ($lng,$lat, $ll2mc) {
      // foreach ($ll2mc as $key => $value) {
      //     echo $value;
      // }
      // 经度的转换比较简单，一个简单的线性转换就可以了。
      // 0、1的数量级别是这样的-0.0015702102444, 111320.7020616939
      $x = $ll2mc[0] + $ll2mc[1] * abs($lng);
      // 先计算一个线性关系，其中9的数量级是这样的：67.5，a的估值大约是一个个位数
      $a = abs($lat) / $ll2mc[9];
      // 维度的转换相对比较复杂，y=b+ca+da^2+ea^3+fa^4+ga^5+ha^6
      // 其中，a是维度的线性转换，而最终值则是一个六次方的多项式，2、3、4、5、6、7、8的数值大约是这样的：
      // 278.2353980772752, 2485758.690035394,
      // 6070.750963243378, 54821.18345352118,
      // 9540.606633304236, -2710.55326746645,
      // 1405.483844121726,
      // 这意味着维度会变成一个很大的数，大到多少很难说
      $y = $ll2mc[2] + $ll2mc[3] * $a + $ll2mc[4] * $a * $a + $ll2mc[5] * $a
      * $a * $a + $ll2mc[6] * $a * $a * $a * $a + $ll2mc[7] * $a
      * $a * $a * $a * $a + $ll2mc[8] * $a * $a * $a * $a
      * $a * $a;
      // 整个计算是基于绝对值的，符号位最后补回去就行了
      $x *= ($lng < 0 ? -1 : 1);
      $y *= ($lat < 0 ? -1 : 1);
      // 产生一个新的点坐标。果然不一样了啊
      $result = array();
      $result[0]=$x;
      $result[1]=$y;
      return $result;
  }

  public static function getLoop($value, $min, $max) {
      while ($value > $max){
          $value -= $max - $min;
      }
      while ($value < $min){
          $value += $max - $min;
      }
      return $value;
  }

  public static function getRange($value, $min, $max) {
      if(!is_null($min)){
          $value = max($value,$min);
      }
      if(!is_null($max)){
           $value = min($value,$max);
      }
      return $value;
  }

  public static function createdir($dir){

      $array_dir=explode("/",$dir);//把多级目录分别放到数组中

      $depth = count($array_dir);
      $path = '';
      for($i=0;$i<$depth;$i++){

          $path .= $array_dir[$i]."/";

          if(!is_dir($path)){
              mkdir($path);
          }
      }
  }

  public static function getcolors($image, $x, $y){
      $hexarray = array();
      $hexarray[0]="00"; $hexarray[1]="01"; $hexarray[2]="02";
      $hexarray[3]="03"; $hexarray[4]="04"; $hexarray[5]="05";
      $hexarray[6]="06"; $hexarray[7]="07"; $hexarray[8]="08";
      $hexarray[9]="09"; $hexarray[10]="0A"; $hexarray[11]="0B";
      $hexarray[12]="0C"; $hexarray[13]="0D"; $hexarray[14]="0E";
      $hexarray[15]="0F"; $hexarray[16]="10"; $hexarray[17]="11";
      $hexarray[18]="12"; $hexarray[19]="13"; $hexarray[20]="14";
      $hexarray[21]="15"; $hexarray[22]="16"; $hexarray[23]="17";
      $hexarray[24]="18"; $hexarray[25]="19"; $hexarray[26]="1A";
      $hexarray[27]="1B"; $hexarray[28]="1C"; $hexarray[29]="1D";
      $hexarray[30]="1E"; $hexarray[31]="1F"; $hexarray[32]="20";
      $hexarray[33]="21"; $hexarray[34]="22"; $hexarray[35]="23";
      $hexarray[36]="24"; $hexarray[37]="25"; $hexarray[38]="26";
      $hexarray[39]="27"; $hexarray[40]="28"; $hexarray[41]="29";
      $hexarray[42]="2A"; $hexarray[43]="2B"; $hexarray[44]="2C";
      $hexarray[45]="2D"; $hexarray[46]="2E"; $hexarray[47]="2F";
      $hexarray[48]="30"; $hexarray[49]="31"; $hexarray[50]="32";
      $hexarray[51]="33"; $hexarray[52]="34"; $hexarray[53]="35";
      $hexarray[54]="36"; $hexarray[55]="37"; $hexarray[56]="38";
      $hexarray[57]="39"; $hexarray[58]="3A"; $hexarray[59]="3B";
      $hexarray[60]="3C"; $hexarray[61]="3D"; $hexarray[62]="3E";
      $hexarray[63]="3F"; $hexarray[64]="40"; $hexarray[65]="41";
      $hexarray[66]="42"; $hexarray[67]="43"; $hexarray[68]="44";
      $hexarray[69]="45"; $hexarray[70]="46"; $hexarray[71]="47";
      $hexarray[72]="48"; $hexarray[73]="49"; $hexarray[74]="4A";
      $hexarray[75]="4B"; $hexarray[76]="4C"; $hexarray[77]="4D";
      $hexarray[78]="4E"; $hexarray[79]="4F"; $hexarray[80]="50";
      $hexarray[81]="51"; $hexarray[82]="52"; $hexarray[83]="53";
      $hexarray[84]="54"; $hexarray[85]="55"; $hexarray[86]="56";
      $hexarray[87]="57"; $hexarray[88]="58"; $hexarray[89]="59";
      $hexarray[90]="5A"; $hexarray[91]="5B"; $hexarray[92]="5C";
      $hexarray[93]="5D"; $hexarray[94]="5E"; $hexarray[95]="6F";
      $hexarray[96]="60"; $hexarray[97]="61"; $hexarray[98]="62";
      $hexarray[99]="63"; $hexarray[100]="64"; $hexarray[101]="65";
      $hexarray[102]="66"; $hexarray[103]="67"; $hexarray[104]="68";
      $hexarray[105]="69"; $hexarray[106]="6A"; $hexarray[107]="6B";
      $hexarray[108]="6C"; $hexarray[109]="6D"; $hexarray[110]="6E";
      $hexarray[111]="6F"; $hexarray[112]="70"; $hexarray[113]="71";
      $hexarray[114]="72"; $hexarray[115]="73"; $hexarray[116]="74";
      $hexarray[117]="75"; $hexarray[118]="76"; $hexarray[119]="77";
      $hexarray[120]="78"; $hexarray[121]="79"; $hexarray[122]="7A";
      $hexarray[123]="7B"; $hexarray[124]="7C"; $hexarray[125]="7D";
      $hexarray[126]="7E"; $hexarray[127]="7F"; $hexarray[128]="80";
      $hexarray[129]="81"; $hexarray[130]="82"; $hexarray[131]="83";
      $hexarray[132]="84"; $hexarray[133]="85"; $hexarray[134]="86";
      $hexarray[135]="87"; $hexarray[136]="88"; $hexarray[137]="89";
      $hexarray[138]="8A"; $hexarray[139]="8B"; $hexarray[140]="8C";
      $hexarray[141]="8D"; $hexarray[142]="8E"; $hexarray[143]="8F";
      $hexarray[144]="90"; $hexarray[145]="91"; $hexarray[146]="92";
      $hexarray[147]="93"; $hexarray[148]="94"; $hexarray[149]="95";
      $hexarray[150]="96"; $hexarray[151]="97"; $hexarray[152]="98";
      $hexarray[153]="99"; $hexarray[154]="9A"; $hexarray[155]="9B";
      $hexarray[156]="9C"; $hexarray[157]="9D"; $hexarray[158]="9E";
      $hexarray[159]="9F"; $hexarray[160]="A0"; $hexarray[161]="A1";
      $hexarray[162]="A2"; $hexarray[163]="A3"; $hexarray[164]="A4";
      $hexarray[165]="A5"; $hexarray[166]="A6"; $hexarray[167]="A7";
      $hexarray[168]="A8"; $hexarray[169]="A9"; $hexarray[170]="AA";
      $hexarray[171]="AB"; $hexarray[172]="AC"; $hexarray[173]="AD";
      $hexarray[174]="AE"; $hexarray[175]="AF"; $hexarray[176]="B0";
      $hexarray[177]="B1"; $hexarray[178]="B2"; $hexarray[179]="B3";
      $hexarray[180]="B4"; $hexarray[181]="B5"; $hexarray[182]="B6";
      $hexarray[183]="B7"; $hexarray[184]="B8"; $hexarray[185]="B9";
      $hexarray[186]="BA"; $hexarray[187]="BB"; $hexarray[188]="BC";
      $hexarray[189]="BD"; $hexarray[190]="BE"; $hexarray[191]="BF";
      $hexarray[192]="C0"; $hexarray[193]="C1"; $hexarray[194]="C2";
      $hexarray[195]="C3"; $hexarray[196]="C4"; $hexarray[197]="C5";
      $hexarray[198]="C6"; $hexarray[199]="C7"; $hexarray[200]="C8";
      $hexarray[201]="C9"; $hexarray[202]="CA"; $hexarray[203]="CB";
      $hexarray[204]="CC"; $hexarray[205]="CD"; $hexarray[206]="CE";
      $hexarray[207]="CF"; $hexarray[208]="D0"; $hexarray[209]="D1";
      $hexarray[210]="D2"; $hexarray[211]="D3"; $hexarray[212]="D4";
      $hexarray[213]="D5"; $hexarray[214]="D6"; $hexarray[215]="D7";
      $hexarray[216]="D8"; $hexarray[217]="D9"; $hexarray[218]="DA";
      $hexarray[219]="DB"; $hexarray[220]="DC"; $hexarray[221]="DD";
      $hexarray[222]="DE"; $hexarray[223]="DF"; $hexarray[224]="E0";
      $hexarray[225]="E1"; $hexarray[226]="E2"; $hexarray[227]="E3";
      $hexarray[228]="E4"; $hexarray[229]="E5"; $hexarray[230]="E6";
      $hexarray[231]="E7"; $hexarray[232]="E8"; $hexarray[233]="E9";
      $hexarray[234]="EA"; $hexarray[235]="EB"; $hexarray[236]="EC";
      $hexarray[237]="ED"; $hexarray[238]="EE"; $hexarray[239]="EF";
      $hexarray[240]="F0"; $hexarray[241]="F1"; $hexarray[242]="F2";
      $hexarray[243]="F3"; $hexarray[244]="F4"; $hexarray[245]="F5";
      $hexarray[246]="F6"; $hexarray[247]="F7"; $hexarray[248]="F8";
      $hexarray[249]="F9"; $hexarray[250]="FA"; $hexarray[251]="FB";
      $hexarray[252]="FC"; $hexarray[253]="FD"; $hexarray[254]="FE";
      $hexarray[255]="FF";
      $returnResult="";
      $colors_reg = imagecolorsforindex($image, imagecolorat($image, $x, $y));
      $colorRe="#".$hexarray[ $colors_reg['red'] ].$hexarray[ $colors_reg['green'] ].$hexarray[ $colors_reg['blue'] ];
      $result="x:".$x.";y:".$y.";color:".$colorRe;

      $colors_array=array("#17BF00","#F19B5B","#F33131","#E2AA24","#C4180F");
      if(in_array($colorRe,$colors_array)){
          return $colorRe;
      }else{
          return "blank";
      }
//      if($colorRe=="#17BF00"){
//          $returnResult = true;
//      }else if($result=="#F19B5B"){
//          $returnResult = true;
//      }else if($result=="#F33131"){
//          $returnResult = true;
//      }else if($result=="#E2AA24"){
//          $returnResult = true;
//      }else{
//          continue;
//      }
//      return $result;
  }

  public static function imagepgm($imgUrl)
  {
    $image = imagecreatefrompng($imgUrl);
    $hexarray = array();
    $hexarray[0]="00"; $hexarray[1]="01"; $hexarray[2]="02";
    $hexarray[3]="03"; $hexarray[4]="04"; $hexarray[5]="05";
    $hexarray[6]="06"; $hexarray[7]="07"; $hexarray[8]="08";
    $hexarray[9]="09"; $hexarray[10]="0A"; $hexarray[11]="0B";
    $hexarray[12]="0C"; $hexarray[13]="0D"; $hexarray[14]="0E";
    $hexarray[15]="0F"; $hexarray[16]="10"; $hexarray[17]="11";
    $hexarray[18]="12"; $hexarray[19]="13"; $hexarray[20]="14";
    $hexarray[21]="15"; $hexarray[22]="16"; $hexarray[23]="17";
    $hexarray[24]="18"; $hexarray[25]="19"; $hexarray[26]="1A";
    $hexarray[27]="1B"; $hexarray[28]="1C"; $hexarray[29]="1D";
    $hexarray[30]="1E"; $hexarray[31]="1F"; $hexarray[32]="20";
    $hexarray[33]="21"; $hexarray[34]="22"; $hexarray[35]="23";
    $hexarray[36]="24"; $hexarray[37]="25"; $hexarray[38]="26";
    $hexarray[39]="27"; $hexarray[40]="28"; $hexarray[41]="29";
    $hexarray[42]="2A"; $hexarray[43]="2B"; $hexarray[44]="2C";
    $hexarray[45]="2D"; $hexarray[46]="2E"; $hexarray[47]="2F";
    $hexarray[48]="30"; $hexarray[49]="31"; $hexarray[50]="32";
    $hexarray[51]="33"; $hexarray[52]="34"; $hexarray[53]="35";
    $hexarray[54]="36"; $hexarray[55]="37"; $hexarray[56]="38";
    $hexarray[57]="39"; $hexarray[58]="3A"; $hexarray[59]="3B";
    $hexarray[60]="3C"; $hexarray[61]="3D"; $hexarray[62]="3E";
    $hexarray[63]="3F"; $hexarray[64]="40"; $hexarray[65]="41";
    $hexarray[66]="42"; $hexarray[67]="43"; $hexarray[68]="44";
    $hexarray[69]="45"; $hexarray[70]="46"; $hexarray[71]="47";
    $hexarray[72]="48"; $hexarray[73]="49"; $hexarray[74]="4A";
    $hexarray[75]="4B"; $hexarray[76]="4C"; $hexarray[77]="4D";
    $hexarray[78]="4E"; $hexarray[79]="4F"; $hexarray[80]="50";
    $hexarray[81]="51"; $hexarray[82]="52"; $hexarray[83]="53";
    $hexarray[84]="54"; $hexarray[85]="55"; $hexarray[86]="56";
    $hexarray[87]="57"; $hexarray[88]="58"; $hexarray[89]="59";
    $hexarray[90]="5A"; $hexarray[91]="5B"; $hexarray[92]="5C";
    $hexarray[93]="5D"; $hexarray[94]="5E"; $hexarray[95]="6F";
    $hexarray[96]="60"; $hexarray[97]="61"; $hexarray[98]="62";
    $hexarray[99]="63"; $hexarray[100]="64"; $hexarray[101]="65";
    $hexarray[102]="66"; $hexarray[103]="67"; $hexarray[104]="68";
    $hexarray[105]="69"; $hexarray[106]="6A"; $hexarray[107]="6B";
    $hexarray[108]="6C"; $hexarray[109]="6D"; $hexarray[110]="6E";
    $hexarray[111]="6F"; $hexarray[112]="70"; $hexarray[113]="71";
    $hexarray[114]="72"; $hexarray[115]="73"; $hexarray[116]="74";
    $hexarray[117]="75"; $hexarray[118]="76"; $hexarray[119]="77";
    $hexarray[120]="78"; $hexarray[121]="79"; $hexarray[122]="7A";
    $hexarray[123]="7B"; $hexarray[124]="7C"; $hexarray[125]="7D";
    $hexarray[126]="7E"; $hexarray[127]="7F"; $hexarray[128]="80";
    $hexarray[129]="81"; $hexarray[130]="82"; $hexarray[131]="83";
    $hexarray[132]="84"; $hexarray[133]="85"; $hexarray[134]="86";
    $hexarray[135]="87"; $hexarray[136]="88"; $hexarray[137]="89";
    $hexarray[138]="8A"; $hexarray[139]="8B"; $hexarray[140]="8C";
    $hexarray[141]="8D"; $hexarray[142]="8E"; $hexarray[143]="8F";
    $hexarray[144]="90"; $hexarray[145]="91"; $hexarray[146]="92";
    $hexarray[147]="93"; $hexarray[148]="94"; $hexarray[149]="95";
    $hexarray[150]="96"; $hexarray[151]="97"; $hexarray[152]="98";
    $hexarray[153]="99"; $hexarray[154]="9A"; $hexarray[155]="9B";
    $hexarray[156]="9C"; $hexarray[157]="9D"; $hexarray[158]="9E";
    $hexarray[159]="9F"; $hexarray[160]="A0"; $hexarray[161]="A1";
    $hexarray[162]="A2"; $hexarray[163]="A3"; $hexarray[164]="A4";
    $hexarray[165]="A5"; $hexarray[166]="A6"; $hexarray[167]="A7";
    $hexarray[168]="A8"; $hexarray[169]="A9"; $hexarray[170]="AA";
    $hexarray[171]="AB"; $hexarray[172]="AC"; $hexarray[173]="AD";
    $hexarray[174]="AE"; $hexarray[175]="AF"; $hexarray[176]="B0";
    $hexarray[177]="B1"; $hexarray[178]="B2"; $hexarray[179]="B3";
    $hexarray[180]="B4"; $hexarray[181]="B5"; $hexarray[182]="B6";
    $hexarray[183]="B7"; $hexarray[184]="B8"; $hexarray[185]="B9";
    $hexarray[186]="BA"; $hexarray[187]="BB"; $hexarray[188]="BC";
    $hexarray[189]="BD"; $hexarray[190]="BE"; $hexarray[191]="BF";
    $hexarray[192]="C0"; $hexarray[193]="C1"; $hexarray[194]="C2";
    $hexarray[195]="C3"; $hexarray[196]="C4"; $hexarray[197]="C5";
    $hexarray[198]="C6"; $hexarray[199]="C7"; $hexarray[200]="C8";
    $hexarray[201]="C9"; $hexarray[202]="CA"; $hexarray[203]="CB";
    $hexarray[204]="CC"; $hexarray[205]="CD"; $hexarray[206]="CE";
    $hexarray[207]="CF"; $hexarray[208]="D0"; $hexarray[209]="D1";
    $hexarray[210]="D2"; $hexarray[211]="D3"; $hexarray[212]="D4";
    $hexarray[213]="D5"; $hexarray[214]="D6"; $hexarray[215]="D7";
    $hexarray[216]="D8"; $hexarray[217]="D9"; $hexarray[218]="DA";
    $hexarray[219]="DB"; $hexarray[220]="DC"; $hexarray[221]="DD";
    $hexarray[222]="DE"; $hexarray[223]="DF"; $hexarray[224]="E0";
    $hexarray[225]="E1"; $hexarray[226]="E2"; $hexarray[227]="E3";
    $hexarray[228]="E4"; $hexarray[229]="E5"; $hexarray[230]="E6";
    $hexarray[231]="E7"; $hexarray[232]="E8"; $hexarray[233]="E9";
    $hexarray[234]="EA"; $hexarray[235]="EB"; $hexarray[236]="EC";
    $hexarray[237]="ED"; $hexarray[238]="EE"; $hexarray[239]="EF";
    $hexarray[240]="F0"; $hexarray[241]="F1"; $hexarray[242]="F2";
    $hexarray[243]="F3"; $hexarray[244]="F4"; $hexarray[245]="F5";
    $hexarray[246]="F6"; $hexarray[247]="F7"; $hexarray[248]="F8";
    $hexarray[249]="F9"; $hexarray[250]="FA"; $hexarray[251]="FB";
    $hexarray[252]="FC"; $hexarray[253]="FD"; $hexarray[254]="FE";
    $hexarray[255]="FF";
    $returnResult="";
    for($y = 0; $y < imagesy($image); $y++)
    {
      for($x = 0; $x < imagesx($image); $x++)
      {
        $colors_reg = imagecolorsforindex($image, imagecolorat($image, $x, $y));
        $colorRe="#".$hexarray[ $colors_reg['red'] ].$hexarray[ $colors_reg['green'] ].$hexarray[ $colors_reg['blue'] ];
        $result="x:".$x.",y:".$y;
        if($colorRe=="#17BF00"){
          $result.=",color:#17BF00;";
        }else if($result=="#FF9F19"){
          $result.=",color:#FF9F19;";
        }else if($result=="#F23030"){
          $result.=",color:#F23030;";
        }else if($result=="#0B0000"){
          $result.=",color:#0B0000;";
        }else{
          continue;
        }
        $returnResult.=$result;
      }
    }
    return $returnResult;
  }

   public static function clustering($point){
        echo "sadgasdg";
//        $x_cur=$point[0]["x"];
//        $y_cur=$point[0]["y"];
//        $result = array();
//        $point = array_splice($point,0,1);
       // echo var_dump($point[0]);
//        foreach ($point as $p){
//            echo $x_cur.$y_cur.var_dump($p);
//        }
        echo "asdgasdgfasdgasdfgsdf";
        return 0;
   }

  public static function checkColors($imgUrl){
      $i = imagecreatefrompng($imgUrl);
      $rTotal=0;$gTotal=0;$bTotal=0;$total=0;
      for ($x=0;$x<imagesx($i);$x++) {
          for ($y=0;$y<imagesy($i);$y++) {
              $rgb = imagecolorat($i,$x,$y);
              $r = ($rgb >> 16) & 0xFF;
              $g = ($rgb >> 8) & 0xFF;
              $b = $rgb & 0xFF;
              // echo "color".$r.$g.$b;
              // if($r==0||$g==0||$b==0){
              //   continue;
              // }
              $rTotal += $r;
              $gTotal += $g;
              $bTotal += $b;
              $total++;
          }
      }
      $rAverage = round($rTotal/$total);
      $gAverage = round($gTotal/$total);
      $bAverage = round($bTotal/$total);
      $result =array($rAverage,$gAverage,$bAverage);
      return Covertor::rgb2html($rAverage,$gAverage,$bAverage);

      // $im  =  imagecreatefrompng ($imgUrl);
      //
      // $rgb  =  imagecolorat ( $im ,  10 ,  15 );
      //
      // $r  = ( $rgb  >>  16 ) &  0xFF ;
      //
      // $g  = ( $rgb  >>  8 ) &  0xFF ;
      //
      // $b  =  $rgb  &  0xFF ;
      //
      // $rgb = array();
      //
      // $rgb['r'] = $r;
      //
      // $rgb['g'] = $g;
      //
      // $rgb['b'] = $rgb;
      // return Covertor::rgb2html($r,$g,$b);
  }

  public static function rgb2html($r, $g=-1, $b=-1)
  {
      if (is_array($r) && sizeof($r) == 3)
          list($r, $g, $b) = $r;
      $r = intval($r); $g = intval($g);
      $b = intval($b);
      $r = dechex($r<0?0:($r>255?255:$r));
      $g = dechex($g<0?0:($g>255?255:$g));
      $b = dechex($b<0?0:($b>255?255:$b));
      $color = (strlen($r) < 2?'0':'').$r;
      $color .= (strlen($g) < 2?'0':'').$g;
      $color .= (strlen($b) < 2?'0':'').$b;
      return '#'.$color;
  }

}
?>
