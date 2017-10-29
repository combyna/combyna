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
        const renderedViewsData = this.props.phpAPI('renderVisibleViews');

        const renderWidget = (widgetData) => {
            if (widgetData.type === 'text') {
                return widgetData.text;
            }

            const childElements = [];

            for (let childWidget of widgetData.children) {
                childElements.push(renderWidget(childWidget));
            }

            if (widgetData.type === 'fragment') {
                return childElements; // No wrapper element needed
            }

            const uniqueID = widgetData.path.join('-');
            const attributes = Object.assign({}, widgetData.attributes, {
                key: uniqueID
            });

            if (widgetData.tag === 'button') {
                // TODO: Factor this out into a separate `ButtonComponent` React component
                attributes.onClick = () => {
                    this.props.phpAPI('dispatchEvent', [widgetData.path]);

                    // TODO: Find a better way
                    this.forceUpdate();
                };
            } else if (widgetData.tag === 'input' && attributes.type === 'text') {
                // TODO: Factor this out into a separate `TextboxComponent` React component

                // FIXME: Needs to either set `defaultValue` or `readOnly`.
            }

            return React.createElement(widgetData.tag, attributes, ...childElements);
        };

        return React.createElement(
            'div',
            {
                className: 'combyna-view'
            },
            ...renderedViewsData.map((renderedViewData) => renderWidget(renderedViewData.widget))
        );
    }
}
