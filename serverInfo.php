<?php  
    /**
     * 获取当前Unix时间戳
     * */
    $unixTimestamp = time();
    /**
     * 获取服务器总的运行时长
     * */
    $serverUptime =  getUpTime();
    /**
     * 获取服务器负载 以及CPU使用信息
     * */
    $serverLoad = GetLoad();
    $cpuUsage = GetCPUInfo();
    /**
     * 获取服务器内存信息
     * */
    $memoryInfo = GetMem();
    
    // 处理IP信息请求
    if(isset($_GET['action']) && $_GET['action'] == 'getip') {
        // 获取用户真实IP地址
        $ip = '';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // 简单过滤掉非法IP
        $ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '未知';
        
        // 获取地理位置信息（只对公网IP有效）
        $location = '';
        // 检查是否是内网IP
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $location = '内网IP';
        } else {
            // 方法1：使用太平洋IP库（覆盖较全面，国内外都支持）
            $url = "http://whois.pconline.com.cn/ipJson.jsp?ip={$ip}&json=true";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36');
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            $response = curl_exec($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            
            if($response && !$errno) {
                // 太平洋的接口返回的是GB2312编码，需要转换
                $response = mb_convert_encoding($response, 'UTF-8', 'GBK');
                $data = json_decode($response, true);
                if($data && isset($data['pro']) && isset($data['city'])) {
                    $region = '';
                    if(!empty($data['pro'])) {
                        $region .= $data['pro'];
                    }
                    if(!empty($data['city']) && $data['city'] != $data['pro']) {
                        $region .= ' ' . $data['city'];
                    }
                    // 根据IP段判断运营商
                    $carrier = judgeCarrier($ip);
                    if($carrier) {
                        $region .= ' ' . $carrier;
                    }
                    $location = $region ? $region : '未知区域';
                }
            }
            
            // 方法2：使用IpInfo API（国外IP支持较好）
            if(empty($location)) {
                $url = "https://ipinfo.io/{$ip}/json";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36');
                $response = curl_exec($ch);
                curl_close($ch);
                
                if($response) {
                    $data = json_decode($response, true);
                    if($data && isset($data['region']) && isset($data['city'])) {
                        $region = '';
                        if(!empty($data['country'])) {
                            $region .= getCountryName($data['country']);
                        }
                        if(!empty($data['region'])) {
                            $region .= ' ' . $data['region'];
                        }
                        if(!empty($data['city'])) {
                            $region .= ' ' . $data['city'];
                        }
                        if(!empty($data['org'])) {
                            $region .= ' ' . preg_replace('/^AS\d+\s+/', '', $data['org']);
                        } else {
                            // 补充运营商信息
                            $carrier = judgeCarrier($ip);
                            if($carrier) {
                                $region .= ' ' . $carrier;
                            }
                        }
                        $location = $region ? $region : '未知区域';
                    }
                }
            }
            
            // 方法3：使用IPIP.net的免费接口（国内移动网络支持较好）
            if(empty($location)) {
                $url = "https://freeapi.ipip.net/{$ip}";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36');
                $response = curl_exec($ch);
                curl_close($ch);
                
                if($response && $response != '{}') {
                    $data = json_decode($response, true);
                    if(is_array($data) && count($data) >= 4) {
                        // IPIP.net返回格式：["中国","福建","厦门","","移动"]
                        $region = '';
                        if(!empty($data[1])) {
                            $region .= $data[1]; // 省份
                        }
                        if(!empty($data[2]) && $data[2] != $data[1]) {
                            $region .= ' ' . $data[2]; // 城市
                        }
                        if(!empty($data[4])) {
                            $region .= ' ' . $data[4]; // 运营商
                        } else {
                            // 补充运营商信息
                            $carrier = judgeCarrier($ip);
                            if($carrier) {
                                $region .= ' ' . $carrier;
                            }
                        }
                        $location = $region ? $region : '未知区域';
                    }
                }
            }
            
            // 方法4：使用淘宝IP库（作为备用）
            if(empty($location)) {
                $url = "https://ip.taobao.com/outGetIpInfo?ip=".$ip."&accessKey=alibaba-inc";
                $opts = array(
                    'http' => array(
                        'method' => "GET",
                        'timeout' => 3,
                    )
                );
                $context = stream_context_create($opts);
                $response = @file_get_contents($url, false, $context);
                
                if($response !== false) {
                    $data = json_decode($response, true);
                    if(isset($data['data']) && $data['data']) {
                        $result = $data['data'];
                        $region = '';
                        if(!empty($result['region'])) {
                            $region .= $result['region'];
                        }
                        if(!empty($result['city']) && $result['city'] != $result['region']) {
                            $region .= ' ' . $result['city'];
                        }
                        if(!empty($result['isp'])) {
                            $region .= ' ' . $result['isp'];
                        } else {
                            // 补充运营商信息
                            $carrier = judgeCarrier($ip);
                            if($carrier) {
                                $region .= ' ' . $carrier;
                            }
                        }
                        $location = $region ? $region : '未知区域';
                    }
                }
            }
            
            // 方法5：极速数据网络IP库（移动网络特别友好）
            if(empty($location)) {
                $url = "http://ip.jisuapi.com/api/ip/geo?ip={$ip}";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($ch);
                curl_close($ch);
                
                if($response) {
                    $data = json_decode($response, true);
                    if(isset($data['result']) && $data['result']) {
                        $result = $data['result'];
                        $region = '';
                        if(!empty($result['province'])) {
                            $region .= $result['province'];
                        }
                        if(!empty($result['city']) && $result['city'] != $result['province']) {
                            $region .= ' ' . $result['city'];
                        }
                        if(!empty($result['isp'])) {
                            $region .= ' ' . $result['isp'];
                        } else {
                            // 补充运营商信息
                            $carrier = judgeCarrier($ip);
                            if($carrier) {
                                $region .= ' ' . $carrier;
                            }
                        }
                        $location = $region ? $region : '未知区域';
                    }
                }
            }
            
            // 方法6：仅使用IP段进行精准匹配（最后保障）
            if(empty($location)) {
                // 根据IP段匹配位置信息
                $ipLocation = getIpLocation($ip);
                if($ipLocation) {
                    $location = $ipLocation;
                } else {
                    // 至少返回运营商信息
                    $carrier = judgeCarrier($ip);
                    if($carrier) {
                        $location = $carrier;
                    } else {
                        $location = "公网IP";
                    }
                }
            }
        }
        
        // 返回IP信息
        echo json_encode(['ip' => $ip, 'location' => $location]);
        exit;
    }
    
    /**
     * 根据IP地址判断归属运营商
     * @param string $ip IP地址
     * @return string 运营商名称
     */
    function judgeCarrier($ip) {
        // 特殊IP段映射
        $special_ip_carriers = [
            // 移动特殊段
            '39.128.' => '中国移动', '39.129.' => '中国移动', '39.130.' => '中国移动', '39.131.' => '中国移动',
            '39.132.' => '中国移动', '39.133.' => '中国移动', '39.134.' => '中国移动', '39.135.' => '中国移动',
            '39.136.' => '中国移动', '39.137.' => '中国移动', '39.138.' => '中国移动', '39.139.' => '中国移动',
            '39.140.' => '中国移动', '39.141.' => '中国移动', '39.142.' => '中国移动', '39.143.' => '中国移动',
            '39.144.' => '中国移动', '39.145.' => '中国移动', '39.146.' => '中国移动', '39.147.' => '中国移动',
            '39.148.' => '中国移动', '39.149.' => '中国移动', '39.150.' => '中国移动', '39.151.' => '中国移动',
            '39.152.' => '中国移动', '39.153.' => '中国移动', '39.154.' => '中国移动', '39.155.' => '中国移动',
            '39.156.' => '中国移动', '39.157.' => '中国移动', '39.158.' => '中国移动', '39.159.' => '中国移动',
            '39.176.' => '中国移动',
            '40.128.' => '中国移动', '40.129.' => '中国移动', '40.130.' => '中国移动',
            '41.128.' => '中国移动', '42.128.' => '中国移动', '43.128.' => '中国移动',
            
            // 联通特殊段
            '44.128.' => '中国联通', '45.128.' => '中国联通', '46.128.' => '中国联通',
            
            // 电信特殊段
            '48.128.' => '中国电信', '49.128.' => '中国电信', '50.128.' => '中国电信', '51.128.' => '中国电信',
        ];
        
        // 检查特殊IP段映射
        foreach ($special_ip_carriers as $prefix => $carrier) {
            if (strpos($ip, $prefix) === 0) {
                return $carrier;
            }
        }
        
        // 移动
        $china_mobile = [
            '39', '40', '41', '42', '43', // 新的移动段
            '47.0', '47.1', '47.2', '47.3', // 兼容部分联通和移动混用
            '178.', '180.', '182.', '183.', '184.', '157.', '158.', '159.', '165.', '172.',
            '120.204.', '120.205.', '120.206.', '120.207.', '120.208.', '120.209.',
            '120.210.', '120.211.', '120.212.', '120.213.', '120.214.', '120.215.',
            '221.130.', '221.131.', '221.132.', '221.133.', '221.134.', '221.135.',
            '221.136.', '221.137.', '221.138.', '221.139.', '211.103.', '211.137.',
        ];
        
        // 联通
        $china_unicom = [
            '44', '45', '46', // 新的联通段
            '47.4', '47.5', '47.6', '47.7', '47.8', '47.9', // 兼容部分联通和移动混用
            '130.', '131.', '132.', '155.', '156.', '186.', '145.', '146.', '166.', '175.',
            '171.', '175.', '176.', '185.', '186.', '166.',
            '120.64.', '120.65.', '120.66.', '120.67.', '120.68.', '120.69.',
            '120.70.', '120.71.', '120.72.', '120.73.', '120.74.', '120.75.',
            '120.76.', '120.77.', '120.78.', '120.79.', '120.80.', '120.81.',
            '121.76.', '121.77.', '121.78.', '121.79.', '121.80.', '121.81.',
            '218.100.', '218.104.', '218.108.', '211.90.', '211.91.', '211.92.',
            '211.93.', '211.94.', '211.95.', '211.96.', '211.97.', '211.98.',
            '211.99.', '211.100.', '211.101.', '211.102.',
        ];
        
        // 电信
        $china_telecom = [
            '48', '49', '50', '51', // 新的电信段
            '133.', '153.', '173.', '177.', '180.', '181.', '189.', '199.',
            '120.128.', '120.129.', '120.130.', '120.131.', '120.132.', '120.133.',
            '120.134.', '120.135.', '120.136.', '120.137.', '120.138.', '120.139.',
            '120.140.', '120.141.', '120.142.', '120.143.', '120.144.', '120.145.',
            '113.64.', '113.65.', '113.66.', '113.67.', '113.68.', '113.69.',
            '113.70.', '113.71.', '113.72.', '113.73.', '113.74.', '113.75.',
            '125.64.', '125.65.', '125.66.', '125.67.', '125.68.', '125.69.',
            '125.70.', '125.71.', '125.72.', '125.73.', '125.74.', '125.75.',
            '210.5.', '210.12.', '210.14.', '210.21.', '210.32.', '210.51.',
            '210.52.', '210.77.', '210.192.',
        ];
        
        // 铁通/广电/其他
        $china_other = [
            '36', '37', '38', // 其他杂段
            '1700', '1705', '1709', // 虚拟运营商
        ];
        
        // 特定省份IP段
        $province_ip_map = [
            // 河南移动IP段范围
            '39.144.25' => '河南 南阳 中国移动',
            '39.144.26' => '河南 南阳 中国移动',
            '39.144.27' => '河南 南阳 中国移动',
            
            // 福建电信IP段范围
            '120.32.2' => '福建 厦门 中国电信',
            '120.32.3' => '福建 厦门 中国电信',
            '120.32.4' => '福建 厦门 中国电信',
        ];
        
        // 检查特定省份IP段映射
        foreach ($province_ip_map as $prefix => $location) {
            if (strpos($ip, $prefix) === 0) {
                return $location;
            }
        }
        
        foreach ($china_mobile as $prefix) {
            if (strpos($ip, $prefix) === 0) {
                return '中国移动';
            }
        }
        
        foreach ($china_unicom as $prefix) {
            if (strpos($ip, $prefix) === 0) {
                return '中国联通';
            }
        }
        
        foreach ($china_telecom as $prefix) {
            if (strpos($ip, $prefix) === 0) {
                return '中国电信';
            }
        }
        
        foreach ($china_other as $prefix) {
            if (strpos($ip, $prefix) === 0) {
                return '其他运营商';
            }
        }
        
        return '';
    }
    
    /**
     * 根据IP前缀识别地理位置
     * @param string $ip IP地址
     * @return string 地理位置
     */
    function getIpLocation($ip) {
        // 特定地区IP段映射表
        $ip_location_map = [
            // 河南移动
            '39.144.2' => '河南 中国移动',
            '39.144.25' => '河南 南阳 中国移动',
            '39.144.26' => '河南 南阳 中国移动',
            
            // 福建电信
            '120.32.2' => '福建 厦门 中国电信',
            '120.32.3' => '福建 厦门 中国电信',
            
            // 北京联通
            '111.200.' => '北京 中国联通',
            '111.201.' => '北京 中国联通',
            
            // 上海电信
            '180.166.' => '上海 中国电信',
            '180.167.' => '上海 中国电信',
            
            // 广东移动
            '120.231.' => '广东 中国移动',
            '120.232.' => '广东 中国移动',
            
            // 浙江电信
            '115.192.' => '浙江 杭州 中国电信',
            '115.193.' => '浙江 杭州 中国电信',
        ];
        
        // 逐段检查，从最长前缀到最短
        $segments = explode('.', $ip);
        
        // 检查前三段 (如 192.168.1)
        if(count($segments) >= 3) {
            $prefix3 = $segments[0].'.'.$segments[1].'.'.$segments[2];
            if(isset($ip_location_map[$prefix3])) {
                return $ip_location_map[$prefix3];
            }
        }
        
        // 检查前两段 (如 192.168)
        if(count($segments) >= 2) {
            $prefix2 = $segments[0].'.'.$segments[1];
            if(isset($ip_location_map[$prefix2])) {
                return $ip_location_map[$prefix2];
            }
        }
        
        // 检查第一段 (如 192)
        if(count($segments) >= 1) {
            $prefix1 = $segments[0];
            if(isset($ip_location_map[$prefix1])) {
                return $ip_location_map[$prefix1];
            }
        }
        
        return '';
    }
    
    /**
     * 获取国家名称（将ISO代码转为中文名称）
     * @param string $code 国家代码
     * @return string 国家名称
     */
    function getCountryName($code) {
        $countries = [
            'CN' => '中国', 'US' => '美国', 'JP' => '日本', 'KR' => '韩国',
            'GB' => '英国', 'DE' => '德国', 'FR' => '法国', 'RU' => '俄罗斯',
            'CA' => '加拿大', 'AU' => '澳大利亚', 'HK' => '香港', 'TW' => '台湾',
        ];
        
        return isset($countries[$code]) ? $countries[$code] : $code;
    }
    
    // 定义要输出的内容  
    $serverInfo = array(  
        'serverTime' => date('Y-m-d H:i:s', $unixTimestamp),  
        'serverUptime' => array(  
            'days' => $serverUptime['days'],  
            'hours' => $serverUptime['hours'],  
            'mins' => $serverUptime['mins'],  
            'secs' => $serverUptime['secs']  
        ),  
        'serverUtcTime' => date('Y/m/d H:i:s', $unixTimestamp),  
        'diskUsage' => array(  
            'value' => disk_total_space(__FILE__) - disk_free_space(__FILE__),  
            'max' => disk_total_space(__FILE__)  
        )
    );  

    $serverStatus = array(  
        'sysLoad' => array($serverLoad['1m'], $serverLoad['5m'], $serverLoad['15m']),  
        'cpuUsage' => array(  
            'user' => $cpuUsage['user'],  
            'nice' => $cpuUsage['nice'],  
            'sys' => $cpuUsage['sys'],  
            'idle' => $cpuUsage['idle']  
        ),  
        'memRealUsage' => array(  
            'value' => $memoryInfo['mRealUsed'],  
            'max' => $memoryInfo['mTotal']  
        ),  
        'memBuffers' => array(  
            'value' => $memoryInfo['mBuffers'],  
            'max' => $memoryInfo['mTotal']  
        ),  
        'memCached' => array(  
            'value' => $memoryInfo['mCached'],  
            'max' => $memoryInfo['mTotal']  
        ),  
        'swapUsage' => array(  
            'value' => $memoryInfo['swapUsed'],  
            'max' => $memoryInfo['swapTotal']  
        ),  
        'swapCached' => array(  
            'value' => $memoryInfo['swapCached'],  
            'max' => $memoryInfo['swapTotal']  
        )  
    ); 
    

 
    
      
    $networkStats = array(  
        'networks' => GetNetwork()
    );  
    // 将以上内容合并为一个数组  
    $output = array(  
        'serverInfo' => $serverInfo,  
        'serverStatus' => $serverStatus,  
        'networkStats' => $networkStats  
    );  
      
    // 将数组转换为JSON字符串并输出  
    echo json_encode($output);  

    /**
     * 获取系统运行时长
     *
     * @return array
     */
    function getUpTime() {  
        $uptime = (float) @file_get_contents("/proc/uptime");  
        $days = floor($uptime / 86400);  
        $hours = floor(($uptime % 86400) / 3600);  
        $minutes = floor((($uptime % 86400) % 3600) / 60);  
        $seconds = ($uptime % 3600) % 60;  
        //$time = $days.":".$hours.":".$minutes.":".$seconds;  
        return array(  
            'days' => $days,  
            'hours' => $hours,  
            'mins' => $minutes,  
            'secs' => $seconds 
        );  
    }


    /**
     * 获取系统负载
     *
     * @return array|false|string[]
     */
    function GetLoad()
    {
        if (false === ($str = file_get_contents("/proc/loadavg")))
            return [];

        $loads = explode(' ', $str);
        if ($loads)
        {
            return [
                '1m'  => $loads[0],
                '5m'  => $loads[1],
                '15m' => $loads[2],
            ];
        }

        return [];
    }
    
    function GetCPUInfo()  
    {  
        $load = sys_getloadavg();  
        $user = $load[0];  
        $nice = $load[1];  
        $sys = $load[2];  
        $idle = 100 - ($user + $nice + $sys);  
        return [  
            'user' => $user,  
            'nice' => $nice,  
            'sys' => $sys,  
            'idle' => $idle,  
        ];  
    }



    /**
     * 内存信息
     *
     * @param bool $bFormat 格式化
     *
     * @return array
     */
    function GetMem(bool $bFormat = false)
    {
        if (false === ($str = file_get_contents("/proc/meminfo")))
            return [];

        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $mems);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

        $mtotal    = $mems[1][0] * 1024;
        $mfree     = $mems[2][0] * 1024;
        $mbuffers  = $buffers[1][0] * 1024;
        $mcached   = $mems[3][0] * 1024;
        $stotal    = $mems[4][0] * 1024;
        $sfree     = $mems[5][0] * 1024;
        $mused     = $mtotal - $mfree;
        $sused     = $stotal - $sfree;
        $mrealused = $mtotal - $mfree - $mcached - $mbuffers; //真实内存使用
        $rtn['mTotal']         = !$bFormat ? $mtotal : $this->size_format($mtotal, 1);
        $rtn['mFree']          = !$bFormat ? $mfree : $this->size_format($mfree, 1);
        $rtn['mBuffers']       = !$bFormat ? $mbuffers : $this->size_format($mbuffers, 1);
        $rtn['mCached']        = !$bFormat ? $mcached : $this->size_format($mcached, 1);
        $rtn['mUsed']          = !$bFormat ? ($mtotal - $mfree) : $this->size_format($mtotal - $mfree, 1);
        $rtn['mPercent']       = (floatval($mtotal) != 0) ? round($mused / $mtotal * 100, 1) : 0;
        $rtn['mRealUsed']      = !$bFormat ? $mrealused : $this->size_format($mrealused, 1);
        $rtn['mRealFree']      = !$bFormat ? ($mtotal - $mrealused) : $this->size_format($mtotal - $mrealused, 1);//真实空闲
        $rtn['mRealPercent']   = (floatval($mtotal) != 0) ? round($mrealused / $mtotal * 100, 1) : 0;             //真实内存使用率
        $rtn['mCachedPercent'] = (floatval($mcached) != 0) ? round($mcached / $mtotal * 100, 1) : 0;              //Cached内存使用率
        $rtn['swapTotal']      = !$bFormat ? $stotal : $this->size_format($stotal, 1);
        $rtn['swapFree']       = !$bFormat ? $sfree : $this->size_format($sfree, 1);
        $rtn['swapUsed']       = !$bFormat ? $sused : $this->size_format($sused, 1);
        $rtn['swapPercent']    = (floatval($stotal) != 0) ? round($sused / $stotal * 100, 1) : 0;
        $rtn['swapCached'] = $mbuffers;
        return $rtn;
    }
    

    /**
     * 获取网络数据
     *
     * @param bool $bFormat
     *
     * @return array
     */
    function GetNetwork(bool $bFormat = false)
    {
        $rtn     = [];
        $netstat = file_get_contents('/proc/net/dev');
        if (false === $netstat)
        {
            return [];
        }

        $bufe = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($bufe as $buf)
        {
            if (preg_match('/:/', $buf))
            {
                list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                $dev_name = trim($dev_name);

                $stats                        = preg_split('/\s+/', trim($stats_list));
                $rtn[$dev_name]['name']       = $dev_name;
                $rtn[$dev_name]['rx']    = !$bFormat ? $stats[0] : $this->netSize($stats[0]);
                $rtn[$dev_name]['in_packets'] = $stats[1];
                $rtn[$dev_name]['in_errors']  = $stats[2];
                $rtn[$dev_name]['in_drop']    = $stats[3];

                $rtn[$dev_name]['tx'] = !$bFormat ? $stats[8] : $this->netSize($stats[8]);
                $rtn[$dev_name]['out_packets'] = $stats[9];
                $rtn[$dev_name]['out_errors']  = $stats[10];
                $rtn[$dev_name]['out_drop']    = $stats[11];
            }
        }

        return $rtn;
    }
