/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

'use strict';

import React from 'react';

/**
 * Class ViewComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
export default class ViewComponent extends React.Component
{
    render() {
        const renderedViewData = this.props.renderView(this.props.name);

        function renderWidget(widgetData) {
            if (widgetData.type === 'text') {
                return widgetData.text;
            }

            let childElements = [];

            for (let childWidget of widgetData.children) {
                childElements.push(renderWidget(childWidget));
            }

            return React.createElement(widgetData.tag, null, ...childElements);
        }

        return React.createElement(
            'div',
            {
                className: 'combyna-view'
            },
            renderWidget(renderedViewData.widget)
        );
    }
}
