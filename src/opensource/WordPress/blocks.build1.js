wp.blocks.registerBlockType('woobooking/border-box', {
    title: 'Booking form',
    icon: 'smiley',
    category: 'woobooking-block',
    attributes: {

    },

    /* This configures how the content and color fields will work, and sets up the necessary elements */

    edit: function(props) {
        console.log("props",props);
        return "sdfsdfsd";
    },
    save: function(props) {
        console.log("props",props);
        return "11111111"
    }
});
wp.blocks.registerBlockType('woobooking/border-box2', {
    title: 'Callout Block',

    icon: 'megaphone',

    category: 'woobooking-block',

    attributes: {

    },

    edit: function( props ) {
        return wp.element.createElement( wp.editor.RichText.Content, {
            tagName: 'div',
            className: 'woo-booking-block-edit',
            value: '<div>sdfdsfdsfd13454534</div>'

        } );

    },

    save: function( props ) {
        return wp.element.createElement( wp.editor.RichText.Content, {
            tagName: 'div',
            className: 'woo-booking-block',
            value: props.attributes.content

        } );
    }
})