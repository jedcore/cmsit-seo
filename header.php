<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width" />
    <title><?php wp_title( ' | ', true, 'right' ); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
    <link href="<?php bloginfo('template_url');?>/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <?php wp_head(); ?>
  </head>
  <body>
    
    <section class="top-bar" data-spy="affix" data-offset-top="60" data-offset-bottom="200">
      <div class="container">
        <div class="row">
          <div class="logo-section">
            <div class="nav-logo"><a href="<?php bloginfo('url');?>"><img src="<?php bloginfo('template_url');?>/images/cmsseologo.png" class="img-responsive"></a></div><!--//nav-logo-->
          </div><!--//logo-section-->
          <div class="navigation-bar hidden-sm hidden-xs">
            <nav class="navbar navbar-default">
              <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="i  con-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav navbar-right">
                      <li><a href="<?php bloginfo('url');?>"><i class="fa fa-home"></i></a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Services <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php bloginfo('url');?>/social-media-marketing">Social Media Marketing</a></li>
                        <li><a href="<?php bloginfo('url');?>/google-optimization">Google Optimization</a></li>
                        <li><a href="<?php bloginfo('url');?>/analytics-and-reporting">Analytics / Reporting</a></li>
                        <li><a href="<?php bloginfo('url');?>/paid-online-marketing">Paid Online Marketing</a></li>
                        <li><a href="<?php bloginfo('url');?>/web-design">Web Design</a></li>
                        <li><a href="<?php bloginfo('url');?>/link-building">Link Building</a></li>
                        <li><a href="<?php bloginfo('url');?>/content-marketing">Content Marketing</a></li>
                        <li><a href="<?php bloginfo('url');?>/on-page-seo">On-page SEO</a></li>
                      </ul>
                    </li>

                    <li><a href="<?php bloginfo('url');?>/category/blog">Blog</a></li>
                    <li><a href="<?php bloginfo('url');?>/contact-us">Contact Us</a></li>
                  </ul>
                
                  
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>
            <div class="cta-nav">
              <div class="pull-right"><a href="<?php bloginfo('url');?>/contact-us"><button class="btn btn-info btn-sm"><strong>CONTACT US NOW FOR A FREE QUOTE</strong></button></a></div>
            </div>
          </div><!--//navigation-bar-->
          <section class="hidden-md hidden-lg mobile-menu">
              <select class="form-control" onChange="if(this.selectedIndex!=0)self.location=this.options[this.selectedIndex].value">
                  <option value=""></option>
                  <option value="<?php bloginfo('url');?>">Home</option>
                  <option value="<?php bloginfo('url');?>/services/social-media-marketing">Social Media Marketing</option>
                  <option value="<?php bloginfo('url');?>/services/google-optimization">Google Optimization</option>
                  <option value="<?php bloginfo('url');?>/services/analytics-and-reporting">Analytics / Reporting</option>
                  <option value="<?php bloginfo('url');?>/services/paid-online-marketing">Paid Online Marketing</option>
                  <option value="<?php bloginfo('url');?>/services/web-design">Web Design</option>
                  <option value="<?php bloginfo('url');?>/services/link-building">Link Building</option>
                  <option value="<?php bloginfo('url');?>/services/content-marketing">Content Marketing</option>
                  <option value="<?php bloginfo('url');?>/services/on-page-seo">On-page SEO</option>
                  <option value="<?php bloginfo('url');?>/category/blog">Blog</option>
                  <option value="<?php bloginfo('url');?>/contact-us">Contact Us</option>
              </select>
          </section>
        </div><!--//row-->
      </div> 
      <!--//top-bar-container-->
    </section>

    

   
