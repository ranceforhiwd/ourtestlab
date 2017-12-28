<nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
                <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar1">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="<?php echo base_url(); ?>index.php/home">Test Lab</a>
                </div>
                <div class="collapse navbar-collapse" id="navbar1">
                        <ul class="nav navbar-nav navbar-right">                                
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">API docs
                                    <span class="caret"></span></a>
                                    <ul id="docs" class="dropdown-menu">
                                        <li id="php"><a>PHP</a></li>
                                        <li id="js"><a href="http://hiwd.biz/jsdoc" target="_blank">JS</a></li>
                                    </ul>
                                </li>
                                <?php if ($this->session->userdata('login')){ ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Modules
                                    <span class="caret"></span></a>
                                    <ul id="modules" class="dropdown-menu"></ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $this->session->userdata('uname'); ?>
                                    <span class="caret"></span></a>
                                    <ul id="user" class="dropdown-menu">
                                      <li><a id="profile" class="sub_menu" href="#">Profile</a></li>
                                      <li><a id="settings" class="sub_menu" href="#">Settings</a></li>                                     
                                    </ul>
                                </li>                                
                                <li><a href="<?php echo base_url(); ?>index.php/home/logout">Log Out</a></li>
                                <?php } else { ?>
                                <li><a id="login" class="header">Login</a></li>
                                <li><a id="signup" class="header">Signup</a></li>
                                <?php } ?>
                        </ul>
                </div>
        </div>
</nav>