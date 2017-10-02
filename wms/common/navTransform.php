<?php 

class naviTransform
    {
        private $pi = 3.14159265358979324;

        //
        // Krasovsky 1940
        //
        // a = 6378245.0, 1/f = 298.3
        // b = a * (1 - f)
        // ee = (a^2 - b^2) / a^2;
        private $a = 6378245.0;
        private $ee = 0.00669342162296594323;

        //
        // World Geodetic System ==> Mars Geodetic System
        public function transform($wgLat, $wgLon)
        {
            if ($this->outOfChina($wgLat, $wgLon))
            {
                $mgLat = $wgLat;
                $mgLon = $wgLon;
                return;
            }
            $dLat = $this->transformLat($wgLon - 105.0, $wgLat - 35.0);
            $dLon = $this->transformLon($wgLon - 105.0, $wgLat - 35.0);
            $radLat = $wgLat / 180.0 * $this->pi;
            $magic = Sin($radLat);
            $magic = 1 - $this->ee * $magic * $magic;
            $sqrtMagic = Sqrt($magic);
            $dLat = ($dLat * 180.0) / (($this->a * (1 - $this->ee)) / ($magic * $sqrtMagic) * $this->pi);
            $dLon = ($dLon * 180.0) / ($this->a / $sqrtMagic * Cos($radLat) * $this->pi);
            $mgLat = $wgLat + $dLat;
            $mgLon = $wgLon + $dLon;
            return array($mgLat,$mgLon);
        }

        function outOfChina($lat, $lon)
        {
            if ($lon < 72.004 || $lon > 137.8347)
                return true;
            if ($lat < 0.8293 || $lat > 55.8271)
                return true;
            return false;
        }

        function transformLat($x, $y)
        {
            $ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y + 0.2 * Sqrt(Abs($x));
            $ret += (20.0 * Sin(6.0 * $x * $this->pi) + 20.0 * Sin(2.0 * $x * $this->pi)) * 2.0 / 3.0;
            $ret += (20.0 * Sin($y * $this->pi) + 40.0 * Sin($y / 3.0 * $this->pi)) * 2.0 / 3.0;
            $ret += (160.0 * Sin($y / 12.0 * $this->pi) + 320 * Sin($y * $this->pi / 30.0)) * 2.0 / 3.0;
            return $ret;
        }

        function transformLon($x, $y)
        {
            $ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1 * Sqrt(Abs($x));
            $ret += (20.0 * Sin(6.0 * $x * $this->pi) + 20.0 * Sin(2.0 * $x * $this->pi)) * 2.0 / 3.0;
            $ret += (20.0 * Sin($x * $this->pi) + 40.0 * Sin($x / 3.0 * $this->pi)) * 2.0 / 3.0;
            $ret += (150.0 * Sin($x / 12.0 * $this->pi) + 300.0 * Sin($x / 30.0 * $this->pi)) * 2.0 / 3.0;
            return $ret;
        }
    }

?>