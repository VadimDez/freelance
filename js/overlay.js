$(function() {
    /*
     the menu list element,
     the container and content divs
     */
    var $menu			= $('#image'),
        $container		= $('#render'),
        $content		= $container.find('.content');

    /*
     lets add the classes effect, e-fade, and e-color to some elements.
     e-fade : this will decrease the opacity of the element
     e-color: this will change the color of the element
     */
    $content
        .find('p')
        .addClass('effect e-fade')
        .end()
        .find('h1, h2, h3')
        .addClass('effect e-fade e-color');

    /*
     elems is all the elements with class effect.
     overlayEffect is our function / module that will take care of the animations
     */
    var $elems			= $(document).find('.effect'),
        OverlayEffect 	= (function(){
            //speed for animations
            var speed				= 700,
            //the event that triggers the effect
                eventOff			= 'mouseenter',
            //the event that stops the effect
                eventOn				= 'mouseleave',
            //this is the color that the elements will have after eventOff
                colorOff			= '#AAAAAA',
            //saves the original color of each e-color element,
            //and calls the methos to initialize the events
                init				= function() {
                    $elems.each(function(){
                        var $el		= $(this);
                        if($el.hasClass('e-color'))
                            $el.data('original-color',$el.css('color'));
                    });
                    initEventsHandler();
                },
            //initializes the events eventOff / eventOn
                initEventsHandler 	= function() {
                    $menu
                        .delegate('a',eventOff,function(e){
                            //relation is the id of the element,
                            //and the class of related elements
                            var relation	= $(this).attr('id');
                            animateElems('off',relation);
                            return false;
                        })
                        .delegate('a',eventOn,function(e){
                            var relation	= $(this).attr('id');
                            animateElems('on',relation);
                            return false;
                        });
                },
            //animates the color and / or opacity
                animateElems		= function(dir,relation) {
                    var $e	= $elems;

                    switch(dir){
                        case 'on'	:
                            //if there are elements on the page with class = relation
                            //then these elements will be excluded for the animation
                            if(relation)
                                $e	= $elems.not('.'+relation);

                            $e.each(function(){
                                var $el		= $(this),
                                    color	= $el.data('original-color'),
                                    param	= {};

                                if($el.hasClass('e-color'))
                                    param.color		= color;
                                if($el.hasClass('e-fade'))
                                    param.opacity	= 1;

                                $el.stop().animate(param,speed);
                            });

                            break;
                        case 'off'	:
                            if(relation)
                                $e	= $elems.not('.'+relation);

                            $e.each(function(){
                                var $el		= $(this),
                                    param	= {};

                                if($el.hasClass('e-color'))
                                    param.color		= colorOff;
                                if($el.hasClass('e-fade'))
                                    param.opacity	= 0.08;

                                $el.stop().animate(param,speed);
                            });

                            break;
                    }
                };

            return {
                init				: init
            };
        })();

    /*
     call the init method of OverlayEffect
     */
    OverlayEffect.init();
});