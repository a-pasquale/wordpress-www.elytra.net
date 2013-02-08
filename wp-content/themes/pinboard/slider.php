<style>
.wp-post-image {
height: 260px;
float: left;
}
.well {
height: 180px;
padding: 19px;
margin: 20px;
margin-top: 50px;
margin-right: 222px;
font-size: 16px;
color: #fff;
text-align: left;
}
.slider-about-content {
    height: 260px;
    width: 844px;
    float: left;
    background: #cb4b16;
}
.slider-about-content .well a {
    color: #2aa198;
}
.slider-about-content .well a:hover {
    text-decoration: underline;
}
.slider-tools-content {
    height: 260px;
    width: 652px;
    float: left;
    background: #268bd2;
}
.slider-tools-content .well {
    margin-top: 68px;
    margin-right: 140px;
}
#about-image {
    width: 296px;
}
#tools-image {
    width:488px;
}
@media screen and (max-width: 1152px) {
    .slider-about-content {
width: 664px;
    }    
    .slider-about-content .well {
         margin-top: 30px;
    }
    .slider-tools-content {
        width: 472px;
    }    
    .slider-tools-content .well {
        margin-top: 42px;
    }
@media screen and (max-width : 960px) {
    .slider-tools-content {
        width: 272px;
    }
    .slider-about-content {
        width: 464px;
    }
    #about-image {
        width: 289px;
    }
    .slider-about-content .well {
        padding: 0;
        margin: 30px 222px 0 20px;
        font-size: 14px;
    }
    .slider-tools-content .well {
        margin:0;
        margin-right:110px;
        font-size:12px;
    }
    .hide-on-tablet {
        display: none;
    }
@media screen and (max-width: 760px) {
    .wp-post-image, .slider-about-content, .slider-tools-content, #about-image, #tools-image {
        height: auto;
        width: 100%;
    }
    .slider-about-content .well, .slider-tools-content .well {
        height: auto;
        margin: 20px;
        width: auto;
        font-size:16px;
    }
    .hide-on-tablet {
        display: block;
    }
}

</style>
        <section id="slider">
			<ul class="slides">
                <li>
                    <article class="post hentry">
                        <a href="/about" title="About me">
                            <img src="/wp-content/uploads/2011/03/IMG_7573_2.jpg" class="wp-post-image"
                                 id="about-image"/>
                        </a>
                        <div class="slider-about-content">
                            <div class="well"><p>I offer design and development services for the web, building everything from blogs to complex web applications.</p><p>Please explore some of the <a href="/?cat=32">projects</a> that I’ve been working on and <a href="/?page_id=62">contact</a> me if you would like to discuss your own undertaking.</p></div>
                        </div>
                        <h2 class="entry-title"><a href="/about" title="About Me" rel="bookmark">About Me</a></h2>
                        <div class="clear"></div>
                    </article><!-- .post -->
                </li>
                <li>
                    <article class="post hentry">
<div>
                        <a href="/tools" title="Tools">
                            <img src="/wp-content/uploads/2013/02/Screen-Shot-2013-02-04-at-2.32.45-PM.png" 
                                 class="wp-post-image" id="tools-image"/>
                        </a>
                        <div class="slider-tools-content">
                            <div class="well">
<p>I’ve been programming computers and the web for more than twenty years and have accumulated some great tools in my toolbox. <span class="hide-on-tablet">Even more important, though, is knowing which tool is appropriate for the job.<span></p>
                            </div>
                        </div>
                        <h2 class="entry-title"><a href="/tools" title="Tools" rel="bookmark">Tools</a></h2>
                        <div class="clear"></div>
                    </article><!-- .post -->
                </li>
			</ul>
			<div class="clear"></div>
		</section>
