(function (wp, React) {
    const { registerPlugin } = wp.plugins;
    const { PluginDocumentSettingPanel } = wp.editPost;
    const { SelectControl, TextControl, BaseControl, RangeControl } = wp.components;
    const { withSelect, withDispatch } = wp.data;
    const { compose } = wp.compose;
    const { useState, useEffect } = wp.element;

    const FontControls = ({ 
        font, 
        weights, 
        size,
        cssSelector, 
        onUpdateFont, 
        onUpdateWeights, 
        onUpdateSize,
        onUpdateCSS, 
        postType 
    }) => {
        const allowedPostTypes = gpppData.enabledPostTypes || ['page'];
        if (!allowedPostTypes.includes(postType)) return null;

        const fonts = gpppData.fonts;
        const defaultFont = gpppData.defaultFont || 'Theme Default';
        const defaultWeights = gpppData.defaultWeights || '400,700';
        const defaultSize = gpppData.defaultSize || '16px';
        const defaultCSS = gpppData.defaultCSS || 'body';
        const fontDisplay = gpppData.fontDisplay || 'swap';

        const [localWeights, setLocalWeights] = useState(weights || defaultWeights);
        const [localSize, setLocalSize] = useState(size || defaultSize);
        const [localCSS, setLocalCSS] = useState(cssSelector || defaultCSS);

        useEffect(() => {
            if (weights !== localWeights) {
                setLocalWeights(weights || defaultWeights);
            }
        }, [weights]);

        useEffect(() => {
            if (size !== localSize) {
                setLocalSize(size || defaultSize);
            }
        }, [size]);

        useEffect(() => {
            if (cssSelector !== localCSS) {
                setLocalCSS(cssSelector || defaultCSS);
            }
        }, [cssSelector]);

        const handleFontChange = (value) => {
            onUpdateFont(value === '' ? '' : value);
        };

        const handleWeightsChange = (value) => {
            setLocalWeights(value);
            onUpdateWeights(value);
        };

        const handleSizeChange = (value) => {
            setLocalSize(value);
            onUpdateSize(value);
        };

        const handleCSSChange = (value) => {
            setLocalCSS(value);
            onUpdateCSS(value);
        };

        return React.createElement(
            PluginDocumentSettingPanel,
            {
                name: 'google-font-panel',
                title: 'Google Font Settings',
                className: 'gppp-enhanced-panel'
            },
            React.createElement(SelectControl, {
                label: 'Select Font',
                value: font || '',
                options: [
                    { label: 'Default (' + defaultFont + ')', value: '' },
                    ...fonts.map(f => ({ label: f.value, value: f.value })),
                ],
                onChange: handleFontChange,
            }),

            font && React.createElement(TextControl, {
                label: 'Font Weights',
                value: localWeights,
                onChange: handleWeightsChange,
                help: 'Comma-separated weights (e.g., 300,400,700)',
            }),

            font && React.createElement(BaseControl, {
                label: 'Font Size',
                help: 'Set the font size for this page',
            },
                React.createElement("div", { style: { display: 'flex', alignItems: 'center' } },
                    React.createElement(RangeControl, {
                        value: parseInt(localSize) || 16,
                        onChange: (value) => handleSizeChange(value + 'px'),
                        min: 8,
                        max: 72,
                        step: 1,
                        style: { flex: 1 }
                    }),
                    React.createElement(TextControl, {
                        value: localSize,
                        onChange: handleSizeChange,
                        style: { width: '80px', marginLeft: '10px' }
                    })
                )
            ),

            font && React.createElement(TextControl, {
                label: 'CSS Selector',
                value: localCSS,
                onChange: handleCSSChange,
                help: 'Where to apply this font (e.g., "body", ".content-area")',
            }),

            font && React.createElement("div", {
                style: {
                    marginTop: "1em",
                    fontFamily: `${font}, sans-serif`,
                    fontSize: localSize,
                    border: "1px solid #ccc",
                    padding: "10px",
                    borderRadius: "4px"
                }
            }, "Live preview: The quick brown fox jumps over the lazy dog."),

            font && React.createElement("div", {
                style: {
                    marginTop: "1em",
                    fontSize: "12px",
                    color: "#666",
                    padding: "8px",
                    backgroundColor: "#f6f7f7",
                    borderRadius: "4px"
                }
            }, 
                `Font will be applied to: ${localCSS}`,
                React.createElement("br"),
                `Font family: ${font}`,
                React.createElement("br"),
                `Font weights: ${localWeights.replace(/\s/g,'')}`,
                React.createElement("br"),
                `Font size: ${localSize}`
            )
        );
    };

    const applyWithSelect = withSelect((select) => {
        const meta = select('core/editor').getEditedPostAttribute('meta');
        return {
            font: meta['_custom_google_font'],
            weights: meta['_custom_font_weights'],
            size: meta['_custom_font_size'],
            cssSelector: meta['_custom_font_css'],
            postType: select('core/editor').getCurrentPostType()
        };
    });

    const applyWithDispatch = withDispatch((dispatch) => {
        return {
            onUpdateFont(font) {
                dispatch('core/editor').editPost({
                    meta: { _custom_google_font: font },
                });
            },
            onUpdateWeights(weights) {
                dispatch('core/editor').editPost({
                    meta: { _custom_font_weights: weights },
                });
            },
            onUpdateSize(size) {
                dispatch('core/editor').editPost({
                    meta: { _custom_font_size: size },
                });
            },
            onUpdateCSS(css) {
                dispatch('core/editor').editPost({
                    meta: { _custom_font_css: css },
                });
            },
        };
    });

    const ConnectedFontControls = compose([
        applyWithSelect,
        applyWithDispatch,
    ])(FontControls);

    registerPlugin('google-font-per-page-enhanced', {
        render: ConnectedFontControls,
    });
})(window.wp, window.React);