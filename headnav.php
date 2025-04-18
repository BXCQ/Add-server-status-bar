 <!-- 这里开始是新追加的内容 -->
        <li class="dropdown pos-stc" id="StateDataPos">
            <a id="StateData" href="#" data-toggle="dropdown" class="dropdown-toggle feathericons dropdown-toggle">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
              <span class="caret"></span>
            </a>
            <div class="dropdown-menu wrapper w-full bg-white">
                <div class="row">
                    <div class="col-sm-4 b-l b-light">
                        <div class="m-t-xs m-b-xs font-bold">运行状态</div>
                        <div class="">
                            <span class="pull-right text-danger" id="cpu">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>CPU占用
                                <span class="badge badge-sm bg-info">2核心</span>
                            </span>
                        </div>
                        <div class="progress progress-xs m-t-sm bg-default">
                            <div id="cpu_css" class="progress-bar bg-danger" data-toggle="tooltip" style="width: 100%"></div>
                        </div>
                        <div class="">
                            <span class="pull-right text-danger" id="memory">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>占用内存
                                <span class="badge badge-sm bg-dark" id="memory_data">
                                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                </span>
                            </span>
                        </div>
                        <div class="progress progress-xs m-t-sm bg-default">
                            <div id="memory_css" class="progress-bar bg-danger" data-toggle="tooltip" style="width: 100%"></div>
                        </div>
                        <div class="">
                            <span class="pull-right text-danger" id="disk">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>磁盘占用
                                <span class="badge badge-sm bg-dark" id="disk_data">
                                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                </span>
                            </span>
                        </div>
                        <div class="progress progress-xs m-t-sm bg-default">
                            <div id="disk_css" class="progress-bar bg-danger" data-toggle="tooltip" style="width: 100%"></div>
                        </div>
                        <div class="">
                            <span class="pull-right text-danger" id="memCached">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>内存缓存
                                <span class="badge badge-sm bg-dark" id="memCached_data">
                                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                </span>
                            </span>
                        </div>
                        <div class="progress progress-xs m-t-sm bg-default">
                            <div id="memCached_css" class="progress-bar bg-danger" data-toggle="tooltip" style="width: 100%"></div>
                        </div>
                        <div class="">
                            <span class="pull-right text-danger" id="memBuffers">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>内存缓冲
                                <span class="badge badge-sm bg-dark" id="memBuffers_data">
                                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                </span>
                            </span>
                        </div>
                        <div class="progress progress-xs m-t-sm bg-default">
                            <div id="memBuffers_css" class="progress-bar bg-danger" data-toggle="tooltip" style="width: 100%"></div>
                        </div>
                        <div class="">
                            <span class="pull-right text-danger" id="state_s">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>系统负载
                                <span id="state">
                                    <span class="badge badge-sm bg-dark">
                                        <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </span>
                        </div>
                        <div class="progress progress-xs m-t-sm bg-default">
                            <div id="state_css" class="progress-bar bg-danger" data-toggle="tooltip" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="col-sm-4 b-l b-light visible-lg visible-md">
                        <div class="m-t-xs m-b-xs font-bold">网络状态</div>
                        <div class="">
                            <span class="pull-right text-default" id="io">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>IO</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="io1">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>实时IO</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="eth">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>网络</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="eth1">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>实时网络</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="time">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>服务器时间</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default">
                                <span class="badge badge-sm bg-dark"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span></span>
                            <span>WEB服务器</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default">
                                <span class="badge badge-sm bg-dark"><?php echo $_SERVER['SERVER_PROTOCOL']; ?></span>
                            </span>
                            <span>通信协议</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default">
                                <span class="badge badge-sm bg-dark"><?php echo PHP_VERSION;?></span>
                            </span>
                            <span>PHP版本</span>
                        </div>
                        <br/>
                    </div>
                    <div class="col-sm-4 b-l b-light visible-lg visible-md">
                        <div class="m-t-xs m-b-sm font-bold">访客信息</div>
                        <div class="">
                            <span class="pull-right text-default" id="u_time">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>持续运行</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="ip">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>您的IP</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="address">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>网络地址</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="b">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>浏览器信息</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="sys">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>您的设备</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default">
                                <span class="badge badge-sm bg-dark"><?php echo $_SERVER['REQUEST_METHOD'];?></span></span>
                            <span>请求方法</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default">
                                <span class="badge badge-sm bg-dark"><?php echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];?></span></span>
                            <span>服务语言</span>
                        </div>
                        <br/>
                        <div class="">
                            <span class="pull-right text-default" id="sys_times">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span>您的设备时间</span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <!-- 新追加的内容到此结束 -->
