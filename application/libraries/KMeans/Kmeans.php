<?php
/**
 * Created by PhpStorm.
 * User: qiang.luis
 * Date: 2017/2/3
 * Time: 15:31
 */
require_once("Cluster.php");
require_once("Point.php");
require_once("Space.php");

class Kmeans{
    public static function solve($points){
        $space = new KMeans\Space(2);
        // add points to space
        foreach ($points as $coordinates)
            $space->addPoint($coordinates);
        $clusters = $space->solve(10);
        return $clusters;
    }
}