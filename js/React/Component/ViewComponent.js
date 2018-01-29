/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
    constructor(props) {
        super(props);

        this.state = {
            visibleViewsState: props.client.createInitialState()
        };
    }

    render() {
        const renderedViewsData = this.props.client.renderVisibleViews(this.state.visibleViewsState);

        const renderWidget = (widgetData) => {
            if (widgetData.type === 'text') {
                return widgetData.text;
            }

            if (widgetData.type === 'widget') {
                // Just render the root element of the widget for now -
                // TODO: properties `widgetData.library` and `.widget` will give the source widget definition
                return renderWidget(widgetData.root);
            }

            if (widgetData.type === 'generic') {
                // FIXME: Check widgetData.library and .widget and create a React element
                //        using the relevant component
                return 'I should be returning a React element';
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
                    const newVisibleViewsState = this.props.client.dispatchEvent(
                        this.state.visibleViewsState,
                        widgetData.path,
                        'gui',
                        'click',
                        {
                            // FIXME: Pass these in from the event data
                            x: 200,
                            y: 100
                        }
                    );

                    this.setState({
                        visibleViewsState: newVisibleViewsState
                    });
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
