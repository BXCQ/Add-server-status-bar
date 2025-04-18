<!-- 这里开始是新追加的内容 -->
<script>
var stateUrl = '/serverInfo.php';
var se_rx;
var se_tx;
var si_rx;
var si_tx;
function returnFloat(value){
    return value.toFixed(2)+'%';
}
function floats(value){
    return value.toFixed(2);
}
function getPercent(curNum, totalNum, isHasPercentStr) {
    curNum = parseFloat(curNum);
    totalNum = parseFloat(totalNum);

    if (isNaN(curNum) || isNaN(totalNum)) {
        return 'Error';
    }

    return isHasPercentStr ?
        totalNum <= 0 ? '0%' : (Math.round(curNum / totalNum * 10000) / 100.00 + '%') :
        totalNum <= 0 ? 0 : (Math.round(curNum / totalNum * 10000) / 100.00  + '%');
}
function getPercents(curNum, totalNum, isHasPercentStr) {
    curNum = parseFloat(curNum);
    totalNum = parseFloat(totalNum);

    if (isNaN(curNum) || isNaN(totalNum)) {
        return 'Error';
    }

    return isHasPercentStr ?
        totalNum <= 0 ? '0%' : (Math.round(curNum / totalNum * 10000) / 100.00) :
        totalNum <= 0 ? 0 : (Math.round(curNum / totalNum * 10000) / 100.00);
}
function setSize(value,d){
    switch (d) {
        case 'bit':
            return bit = value*8;
            break;
        case 'bytes':
            return value;
            break;
        case 'kb':
            return value/1024;
            break;
        case 'mb':
            return value/1024/1024;
            break;
        case 'gb':
            return value/1024/1024/1024;
            break;
        case 'tb':
            return value/1024/1024/1024/1024;
            break;
    }
}
function ForDight(Dight){ 
    if (Dight < 0){
        var Last = 0+"B/s";
    }else if (Dight < 1024){
        var Last = setSize(Dight,'bytes').toFixed(0)+"B/s";
    }else if (Dight < 1048576){
        var Last = floats(setSize(Dight,'kb'))+"K/s";
    }else{
        var Last = floats(setSize(Dight,'mb'))+"MB/s";
    }
    return Last; 
}
function state(){
    $.ajax({
        url: stateUrl,
        type: 'get',
        dataType: 'json',
        data: {
            action:'fetch',
        },
        beforeSend:function(){
            
        },
        complete:function(){
            
        },
        error: function() {
        }, 
        success: function (data) {
            var cpu = data.serverStatus.cpuUsage['user']+data.serverStatus.cpuUsage['nice']+data.serverStatus.cpuUsage['sys'];
            $("#cpu").html(returnFloat(cpu));
            $("#cpu_css").css("width",returnFloat(cpu));
            if(cpu<70){
                $("#cpu_css").removeClass();
                $("#cpu_css").addClass("progress-bar bg-success");
                $("#cpu").removeClass();
                $("#cpu").addClass("pull-right text-success");
            }
            if(cpu>=70){
                $("#cpu_css").removeClass();
                $("#cpu_css").addClass("progress-bar bg-warning");
                $("#cpu").removeClass();
                $("#cpu").addClass("pull-right text-warning");
            }
            if(cpu>=90){
                $("#cpu_css").removeClass();
                $("#cpu_css").addClass("progress-bar bg-danger");
                $("#cpu").removeClass();
                $("#cpu").addClass("pull-right text-danger");
            }
            
            var memory_value = data.serverStatus.memRealUsage['value'];
            var memory_max = data.serverStatus.memRealUsage['max'];
            $("#memory").html(getPercent(memory_value,memory_max,memory_value));
            window.RemData = getPercents(memory_value,memory_max,memory_value);
            $("#memory_css").css("width",getPercent(memory_value,memory_max,memory_value));
            var me = getPercents(memory_value,memory_max,memory_value);
            if(me<70){
                $("#memory_css").removeClass();
                $("#memory_css").addClass("progress-bar bg-success");
                $("#memory").removeClass();
                $("#memory").addClass("pull-right text-success");
            }
            if(me>=70){
                $("#memory_css").removeClass();
                $("#memory_css").addClass("progress-bar bg-warning");
                $("#memory").removeClass();
                $("#memory").addClass("pull-right text-warning");
            }
            if(me>=90){
                $("#memory_css").removeClass();
                $("#memory_css").addClass("progress-bar bg-danger");
                $("#memory").removeClass();
                $("#memory").addClass("pull-right text-danger");
            }
            if(floats(setSize(memory_value,'mb'))>1024){
                var memory_data_value = floats(setSize(memory_value,'gb'))+"GB";
            } else{
                var memory_data_value = floats(setSize(memory_value,'mb'))+"MB";
            }
            if(floats(setSize(memory_max,'mb'))>1024){
                var memory_data_max = floats(setSize(memory_max,'gb'))+"GB";
            } else{
                var memory_data_max = floats(setSize(memory_max,'mb'))+"MB";
            }
            $("#memory_data").html(memory_data_value+" / "+memory_data_max);
            
            var disk_value = data.serverInfo.diskUsage['value'];
            var disk_max = data.serverInfo.diskUsage['max'];
            $("#disk").html(getPercent(disk_value,disk_max,disk_value));
            $("#disk_css").css("width",getPercent(disk_value,disk_max,disk_value));
            var dk = getPercents(disk_value,disk_max,disk_value);
            if(dk<70){
                $("#disk_css").removeClass();
                $("#disk_css").addClass("progress-bar bg-success");
                $("#disk").removeClass();
                $("#disk").addClass("pull-right text-success");
            }
            if(dk>=70){
                $("#disk_css").removeClass();
                $("#disk_css").addClass("progress-bar bg-warning");
                $("#disk").removeClass();
                $("#disk").addClass("pull-right text-warning");
            }
            if(dk>=90){
                $("#disk_css").removeClass();
                $("#disk_css").addClass("progress-bar bg-danger");
                $("#disk").removeClass();
                $("#disk").addClass("pull-right text-danger");
            }
            if(floats(setSize(disk_value,'mb'))>1024){
                var disk_data_value = floats(setSize(disk_value,'gb'))+"GB";
            } else{
                var disk_data_value = floats(setSize(disk_value,'mb'))+"MB";
            }
            if(floats(setSize(disk_max,'mb'))>1024){
                var disk_data_max = floats(setSize(disk_max,'gb'))+"GB";
            } else{
                var disk_data_max = floats(setSize(disk_max,'mb'))+"MB";
            }
            $("#disk_data").html(disk_data_value+" / "+disk_data_max);
            
            var memCached_value = data.serverStatus.memCached['value'];
            var memCached_max = data.serverStatus.memCached['max'];
            $("#memCached").html(getPercent(memCached_value,memCached_max,memCached_value));
            $("#memCached_css").css("width",getPercent(memCached_value,memCached_max,memCached_value));
            var mem = getPercents(memCached_value,memCached_max,memCached_value);
            if(mem<70){
                $("#memCached_css").removeClass();
                $("#memCached_css").addClass("progress-bar bg-success");
                $("#memCached").removeClass();
                $("#memCached").addClass("pull-right text-success");
            }
            if(mem>=70){
                $("#memCached_css").removeClass();
                $("#memCached_css").addClass("progress-bar bg-warning");
                $("#memCached").removeClass();
                $("#memCached").addClass("pull-right text-warning");
            }
            if(mem>=90){
                $("#memCached_css").removeClass();
                $("#memCached_css").addClass("progress-bar bg-danger");
                $("#memCached").removeClass();
                $("#memCached").addClass("pull-right text-danger");
            }
            if(floats(setSize(memCached_value,'mb'))>1024){
                var memCached_data_value = floats(setSize(memCached_value,'gb'))+"GB";
            } else{
                var memCached_data_value = floats(setSize(memCached_value,'mb'))+"MB";
            }
            if(floats(setSize(memCached_max,'mb'))>1024){
                var memCached_data_max = floats(setSize(memCached_max,'gb'))+"GB";
            } else{
                var memCached_data_max = floats(setSize(memCached_max,'mb'))+"MB";
            }
            $("#memCached_data").html(memCached_data_value+" / "+memCached_data_max);
            
            var memBuffers_value = data.serverStatus.memBuffers['value'];
            var memBuffers_max = data.serverStatus.memBuffers['max'];
            $("#memBuffers").html(getPercent(memBuffers_value,memBuffers_max,memBuffers_value));
            $("#memBuffers_css").css("width",getPercent(memBuffers_value,memBuffers_max,memBuffers_value));
            var memB = getPercents(memCached_value,memCached_max,memCached_value);
            if(memB<70){
                $("#memBuffers_css").removeClass();
                $("#memBuffers_css").addClass("progress-bar bg-success");
                $("#memBuffers").removeClass();
                $("#memBuffers").addClass("pull-right text-success");
            }
            if(memB>=70){
                $("#memBuffers_css").removeClass();
                $("#memBuffers_css").addClass("progress-bar bg-warning");
                $("#memBuffers").removeClass();
                $("#memBuffers").addClass("pull-right text-warning");
            }
            if(memB>=90){
                $("#memBuffers_css").removeClass();
                $("#memBuffers_css").addClass("progress-bar bg-danger");
                $("#memBuffers").removeClass();
                $("#memBuffers").addClass("pull-right text-danger");
            }
            if(floats(setSize(memBuffers_value,'mb'))>1024){
                var memBuffers_data_value = floats(setSize(memBuffers_value,'gb'))+"GB";
            } else{
                var memBuffers_data_value = floats(setSize(memBuffers_value,'mb'))+"MB";
            }
            if(floats(setSize(memBuffers_max,'mb'))>1024){
                var memBuffers_data_max = floats(setSize(memBuffers_max,'gb'))+"GB";
            } else{
                var memBuffers_data_max = floats(setSize(memBuffers_max,'mb'))+"MB";
            }
            $("#memBuffers_data").html(memBuffers_data_value+" / "+memBuffers_data_max);
            
            var state = "";
            for(var i = 0; i < data.serverStatus.sysLoad.length ; i++){
                state += '<span class="badge badge-sm bg-dark">'+data.serverStatus.sysLoad[i]+'</span>&nbsp;'
            }
            $("#state").html(state);
            var state_s = getPercent(data.serverStatus.sysLoad[0],2,data.serverStatus.sysLoad[0]);
            $("#state_css").css("width",state_s);
            $("#state_s").html(state_s);
            var sta = getPercents(data.serverStatus.sysLoad[0],2,data.serverStatus.sysLoad[0]);
            if(sta<70){
                $("#state_css").removeClass();
                $("#state_css").addClass("progress-bar bg-success");
                $("#state_s").removeClass();
                $("#state_s").addClass("pull-right text-success");
            }
            if(sta>=70){
                $("#state_css").removeClass();
                $("#state_css").addClass("progress-bar bg-warning");
                $("#state_s").removeClass();
                $("#state_s").addClass("pull-right text-warning");
            }
            if(sta>=90){
                $("#state_css").removeClass();
                $("#state_css").addClass("progress-bar bg-danger");
                $("#state_s").removeClass();
                $("#state_s").addClass("pull-right text-danger");
            }
            
            $("#time").html('<span class="badge badge-sm bg-dark">'+data.serverInfo.serverTime+'</span>');
            
            $("#u_time").html('<span class="badge badge-sm bg-dark">'+data.serverInfo.serverUptime["days"]+' 天'+data.serverInfo.serverUptime["hours"]+' 时 '+data.serverInfo.serverUptime["mins"]+' 分'+data.serverInfo.serverUptime["secs"]+' 秒</span>');
            
            if(floats(setSize(data.networkStats.networks.eth0.tx,'mb'))>1024){
                var aaa_tx = floats(setSize(data.networkStats.networks.eth0.tx,'gb'))+"GB";
            } else{
                var aaa_tx = floats(setSize(data.networkStats.networks.eth0.tx,'mb'))+"MB";
            }
            if(floats(setSize(data.networkStats.networks.eth0.rx,'mb'))>1024){
                var aaa_rx = floats(setSize(data.networkStats.networks.eth0.rx,'gb'))+"GB";
            } else{
                var aaa_rx = floats(setSize(data.networkStats.networks.eth0.rx,'mb'))+"MB";
            }
        
            $("#eth").html('<span class="badge badge-sm bg-dark"><i class="glyphicon glyphicon-upload" aria-hidden="true"></i>&nbsp;'+aaa_tx+'</span>&nbsp;'+
            '<span class="badge badge-sm bg-dark"><i class="glyphicon glyphicon-download" aria-hidden="true"></i>&nbsp;'+aaa_rx+'</span>');
            $("#eth1").html('<span class="badge badge-sm bg-dark"><i class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></i>&nbsp;'+ForDight(data.networkStats.networks.eth0.tx-se_tx,3)+'</span>&nbsp;'+
            '<span class="badge badge-sm bg-dark"><i class="glyphicon glyphicon-cloud-download" aria-hidden="true"></i>&nbsp;'+ForDight(data.networkStats.networks.eth0.rx-se_rx,3)+'</span>');
            se_tx = data.networkStats.networks.eth0.tx;
            se_rx = data.networkStats.networks.eth0.rx;
            if(floats(setSize(data.networkStats.networks.lo.tx,'mb'))>1024){
                var lo_tx = floats(setSize(data.networkStats.networks.lo.tx,'gb'))+"GB";
            } else{
                var lo_tx = floats(setSize(data.networkStats.networks.lo.tx,'mb'))+"MB";
            }
            if(floats(setSize(data.networkStats.networks.lo.rx,'mb'))>1024){
                var lo_rx = floats(setSize(data.networkStats.networks.lo.rx,'gb'))+"GB";
            } else{
                var lo_rx = floats(setSize(data.networkStats.networks.lo.rx,'mb'))+"MB";
            }
            $("#io").html('<span class="badge badge-sm bg-success"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;'+lo_tx+'</span>&nbsp;'+
            '<span class="badge badge-sm bg-success"><i class="glyphicon glyphicon-cloud-download" aria-hidden="true"></i>&nbsp;'+lo_rx+'</span>');
            $("#io1").html('<span class="badge badge-sm bg-dark"><i class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></i>&nbsp;'+ForDight(data.networkStats.networks.lo.tx-si_tx,3)+'</span>&nbsp;'+
            '<span class="badge badge-sm bg-dark"><i class="glyphicon glyphicon-cloud-download" aria-hidden="true"></i>&nbsp;'+ForDight(data.networkStats.networks.lo.rx-si_rx,3)+'</span>');
            si_tx = data.networkStats.networks.lo.tx;
            si_rx = data.networkStats.networks.lo.rx;
        }
    });
}
function getNowFormatDate(){
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
      month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
      strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
          + " " + date.getHours() + seperator2 + date.getMinutes()
          + seperator2 + date.getSeconds();
    return currentdate;
}
function UserInfo(){
    // 先根据UA设置设备和浏览器信息
    var ua = navigator.userAgent;
    var browserInfo = getBrowserInfo(ua);
    var osInfo = getOsInfo(ua);
    
    // 立即显示浏览器和系统信息（不依赖API）
    $("#b").html('<span class="badge badge-sm bg-dark">'+browserInfo+'</span>');
    $("#sys").html('<span class="badge badge-sm bg-dark">'+osInfo+'</span>');
    
    // 使用我们自己的serverInfo.php获取IP信息，避免跨域问题
    $.ajax({
        type: "get",
        url: stateUrl, // 使用之前已定义的stateUrl变量 (指向serverInfo.php)
        data: {action: 'getip'},
        async: true,
        dataType: "json",
        beforeSend: function(){
            $("#ip").html('<span class="badge badge-sm bg-dark">获取中...</span>');
            $("#address").html('<span class="badge badge-sm bg-dark">获取中...</span>');
        },
        error: function(){
            $("#ip").html('<span class="badge badge-sm bg-dark">'+window.location.hostname+'</span>');
            $("#address").html('<span class="badge badge-sm bg-dark">本地访问</span>');
        },
        success: function(data){
            if(data && data.ip) {
                $("#ip").html('<span class="badge badge-sm bg-dark">'+data.ip+'</span>');
                
                if(data.location) {
                    $("#address").html('<span class="badge badge-sm bg-dark">'+data.location+'</span>');
                } else {
                    $("#address").html('<span class="badge badge-sm bg-dark">本地网络</span>');
                }
            } else {
                $("#ip").html('<span class="badge badge-sm bg-dark">'+window.location.hostname+'</span>');
                $("#address").html('<span class="badge badge-sm bg-dark">本地访问</span>');
            }
        }
    });
}

// 获取设备系统信息的函数
function getOsInfo(ua) {
    if(!ua) ua = navigator.userAgent;
    
    if (ua.indexOf("Windows NT 10.0") != -1) {
        // 检测Win11
        if (ua.indexOf("Windows NT 10.0; Win64") != -1 && 
            ((ua.indexOf("Chrome/") != -1 && parseInt(ua.split("Chrome/")[1]) >= 90) || 
             (ua.indexOf("Firefox/") != -1 && parseInt(ua.split("Firefox/")[1]) >= 90))) {
            return "Windows 11";
        }
        return "Windows 10";
    }
    if (ua.indexOf("Windows NT 6.3") != -1) return "Windows 8.1";
    if (ua.indexOf("Windows NT 6.2") != -1) return "Windows 8";
    if (ua.indexOf("Windows NT 6.1") != -1) return "Windows 7";
    if (ua.indexOf("Windows NT 6.0") != -1) return "Windows Vista";
    if (ua.indexOf("Windows NT 5.1") != -1) return "Windows XP";
    if (ua.indexOf("Android") != -1) return "Android " + (ua.match(/Android [\d\.]+;/) ? ua.match(/Android [\d\.]+;/)[0].replace(";","").replace("Android ","") : "");
    if (ua.indexOf("iPhone") != -1) return "iPhone";
    if (ua.indexOf("iPad") != -1) return "iPad";
    if (ua.indexOf("Mac OS X") != -1) return "macOS";
    if (ua.indexOf("Linux") != -1) return "Linux";
    if (ua.indexOf("CentOS") != -1) return "CentOS";
    if (ua.indexOf("Ubuntu") != -1) return "Ubuntu";
    
    return "未知设备";
}

// 获取浏览器信息
function getBrowserInfo(ua) {
    if(!ua) ua = navigator.userAgent;
    
    // Edge
    if (ua.indexOf("Edg/") > -1) {
        return "Edge " + ua.match(/Edg\/([\d.]+)/)[1].split('.')[0];
    }
    // Chrome
    else if (ua.indexOf("Chrome/") > -1 && ua.indexOf("Safari/") > -1 && ua.indexOf("OPR/") == -1) {
        return "Chrome " + ua.match(/Chrome\/([\d.]+)/)[1].split('.')[0];
    }
    // Firefox
    else if (ua.indexOf("Firefox/") > -1) {
        return "Firefox " + ua.match(/Firefox\/([\d.]+)/)[1].split('.')[0];
    }
    // Opera
    else if (ua.indexOf("OPR/") > -1 || ua.indexOf("Opera/") > -1) {
        return "Opera " + (ua.match(/OPR\/([\d.]+)/) || ua.match(/Opera\/([\d.]+)/))[1].split('.')[0];
    }
    // Safari
    else if (ua.indexOf("Safari/") > -1 && ua.indexOf("Chrome/") == -1) {
        return "Safari " + (ua.match(/Version\/([\d.]+)/) ? ua.match(/Version\/([\d.]+)/)[1].split('.')[0] : "");
    }
    // 其他浏览器
    else {
        return "浏览器 " + (navigator.appVersion ? navigator.appVersion.split(" ")[0] : "");
    }
}

$('#StateData').click(function(){
    clearInterval(window.getnet);
    clearInterval(window.info);
    window.getnet = setInterval(function(){
        if($('#StateDataPos').is('.open')){
            state();
            $("#sys_times").html('<span class="badge badge-sm bg-dark">'+getNowFormatDate()+'</span>');
        }
    },1000);
    UserInfo();
});
</script>
<!-- 新追加的内容到此结束 -->
