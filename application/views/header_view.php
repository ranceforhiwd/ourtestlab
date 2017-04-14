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
                                <li><a id="docs" class="main_menu">API Docs</a></li>
                                <?php if ($this->session->userdata('login')){ ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Modules
                                    <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                      <li><a href="#">mod-1</a></li>
                                      <li><a href="#">mod-2</a></li>                                     
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $this->session->userdata('uname'); ?>
                                    <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                      <li><a id="profile" class="sub_menu" href="#">Profile</a></li>
                                      <li><a id="settings" class="sub_menu" href="#">Settings</a></li>                                     
                                    </ul>
                                </li>
                                <!--<li><p class="navbar-text">Hello <?php //echo $this->session->userdata('uname'); ?></p></li>-->
                                <li><a href="<?php echo base_url(); ?>index.php/home/logout">Log Out</a></li>
                                <?php } else { ?>
                                <li><a id="login" class="header">Login</a></li>
                                <li><a id="signup" class="header">Signup</a></li>
                                <?php } ?>
                        </ul>
                </div>
        </div>
</nav>