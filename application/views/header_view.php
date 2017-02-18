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
                                <?php if ($this->session->userdata('login')){ ?>
                                <li><p class="navbar-text">Hello <?php echo $this->session->userdata('uname'); ?></p></li>
                                <li><a href="<?php echo base_url(); ?>index.php/home/logout">Log Out</a></li>
                                <?php } else { ?>
                                <li><a id="login" class="header">Login</a></li>
                                <li><a id="signup" class="header">Signup</a></li>
                                <?php } ?>
                        </ul>
                </div>
        </div>
</nav>