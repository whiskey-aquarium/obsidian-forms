/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./blocks/src/form/components/ObsidianFormSettings.js":
/*!************************************************************!*\
  !*** ./blocks/src/form/components/ObsidianFormSettings.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


/**
 * Component for rendering form settings.
 *
 * @param {Object} props Props passed to the component.
 * @return {Object} The rendered component.
 */

const ObsidianFormSettings = props => {
  const {
    formSettings,
    handleSettingChange
  } = props;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.Fragment, {
    children: formSettings &&
    // Loop through the formSettings object
    Object.entries(formSettings).map(([key, settings]) => {
      // Depending on the 'type', render different controls
      if (settings.type === 'select') {
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
          label: settings.label,
          value: settings.value,
          options: settings.options,
          onChange: value => handleSettingChange(key, value)
        }, key);
      }
      if (settings.type === 'radio') {
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.RadioControl, {
          label: settings.label,
          selected: settings.value,
          options: settings.options,
          onChange: value => handleSettingChange(key, value)
        }, key);
      }
      if (settings.type === 'toggle') {
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
          label: settings.label,
          checked: settings.value,
          onChange: value => handleSettingChange(key, value)
        }, key);
      }
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.TextControl, {
        label: settings.label,
        value: settings.value,
        onChange: value => handleSettingChange(key, value)
      }, key);
    })
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ObsidianFormSettings);

/***/ }),

/***/ "./blocks/src/form/edit.js":
/*!*********************************!*\
  !*** ./blocks/src/form/edit.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _components_ObsidianFormSettings__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/ObsidianFormSettings */ "./blocks/src/form/components/ObsidianFormSettings.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__);









function Edit(props) {
  const {
    attributes,
    setAttributes
  } = props;
  const {
    formPostId,
    formSettings
  } = attributes;

  // State to manage form creation and selection
  const [isCreatingNew, setIsCreatingNew] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(false);
  const [newFormTitle, setNewFormTitle] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)('');
  const [selectedForm, setSelectedForm] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(null);
  const [availableForms, setAvailableForms] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)([]);
  const [isLoadingForms, setIsLoadingForms] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(true);
  const [noFormsAvailable, setNoFormsAvailable] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(false);
  const [blocks, onInput, onChange] = (0,_wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__.useEntityBlockEditor)('postType', 'obsidian_form', {
    id: formPostId || 0
  });
  const [title, setTitle] = (0,_wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__.useEntityProp)('postType', 'obsidian_form', 'title', formPostId || 0);
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.useBlockProps)({
    className: 'obsidian-form__editor obsidian-form__editor--ready'
  });
  const innerBlocksProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.useInnerBlocksProps)({}, {
    value: blocks,
    onInput,
    onChange,
    allowedBlocks: ['obsidian-form/field-group'],
    renderAppender: blocks?.length ? undefined : _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InnerBlocks.ButtonBlockAppender
  });
  const fetchAllForms = async () => {
    let page = 1;
    let allForms = [];
    let totalPages = 1;
    while (page <= totalPages) {
      try {
        const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
          path: `/wp/v2/obsidian_form?per_page=100&page=${page}`,
          parse: false
        });

        // Parse the JSON body
        const forms = await response.json();
        if (forms.length > 0) {
          allForms = allForms.concat(forms);
          // Update totalPages based on the header information
          totalPages = parseInt(response.headers.get('X-WP-TotalPages'), 10);
          page++;
        } else {
          break;
        }
      } catch (error) {
        break;
      }
    }
    return allForms;
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useEffect)(() => {
    if (!formPostId) {
      setIsLoadingForms(true);
      fetchAllForms().then(forms => {
        if (forms.length === 0) {
          setNoFormsAvailable(true);
        } else {
          const options = forms.map(form => ({
            label: `${form.title.rendered} (ID: ${form.id})`,
            value: form.id
          }));
          setAvailableForms(options);
          setNoFormsAvailable(false);
        }
      }).finally(() => {
        setIsLoadingForms(false);
      });
    }
  }, [formPostId]);
  const handleCreateNewForm = async () => {
    if (newFormTitle.trim()) {
      // Create a new form using the REST API
      const createdForm = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
        path: '/wp/v2/obsidian_form',
        method: 'POST',
        data: {
          title: newFormTitle,
          content: `<!-- wp:obsidian-form/field-group -->
<!-- wp:obsidian-form/field {"isRequired":true} /-->
<!-- /wp:obsidian-form/field-group -->`,
          status: 'publish'
        }
      });
      if (createdForm?.id) {
        setAttributes({
          formPostId: createdForm.id
        });
        setIsCreatingNew(false);
      }
    }
  };
  const handleSelectExistingForm = () => {
    if (selectedForm) {
      setAttributes({
        formPostId: selectedForm
      });
    }
  };
  const handleCopyExistingForm = async () => {
    if (selectedForm) {
      try {
        const formToCopy = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
          path: `/wp/v2/obsidian_form/${selectedForm}`
        });
        if (formToCopy?.id) {
          const newForm = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
            path: '/wp/v2/obsidian_form',
            method: 'POST',
            data: {
              title: `${formToCopy.title.rendered} (Copy)`,
              content: formToCopy.raw_content,
              status: 'publish'
            }
          });
          if (newForm?.id) {
            setAttributes({
              formPostId: newForm.id
            });
          }
        }
      } catch (error) {
        console.error('Error copying form:', error);
        // TODO: Show the error notice in the UI
      }
    }
  };
  const handleSettingChange = (key, value) => {
    const newSettings = {
      ...formSettings,
      [key]: {
        ...formSettings[key],
        value
      }
    };
    setAttributes({
      formSettings: newSettings
    });
  };
  if (formPostId) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
          header: "Obsidian Form Settings",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_components_ObsidianFormSettings__WEBPACK_IMPORTED_MODULE_7__["default"], {
            formSettings: formSettings,
            handleSettingChange: handleSettingChange
          })
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("form", {
        ...blockProps,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.RichText, {
          value: title,
          onChange: setTitle,
          placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Enter Form Title', 'obsidian-forms'),
          tagName: "h2"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("div", {
          className: "wp-block-obsidian-form-fields",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("div", {
            ...innerBlocksProps
          })
        })]
      })]
    });
  }

  // Render UI to create or select a form if no formPostId is set
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Placeholder, {
    icon: "feedback",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Obsidian Form', 'obsidian-forms'),
    instructions: noFormsAvailable ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create a new form to get started!', 'obsidian-forms') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create a new form or select an existing one.', 'obsidian-forms'),
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
      className: "obsidian-forms-choice",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
        className: "obsidian-forms-choice__form-creator",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
          placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('New form title…', 'obsidian-forms'),
          value: newFormTitle,
          onChange: setNewFormTitle
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isPrimary: true,
          onClick: handleCreateNewForm,
          disabled: !newFormTitle.trim(),
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create', 'obsidian-forms')
        })]
      }), !noFormsAvailable && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
        className: "obsidian-forms-choice__form-selector",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ComboboxControl, {
          value: selectedForm,
          options: availableForms,
          onChange: value => setSelectedForm(value),
          shouldShowMenuOnFocus: true,
          disabled: isLoadingForms
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
          className: "button-group",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            isPrimary: true,
            onClick: handleSelectExistingForm,
            disabled: !selectedForm || isLoadingForms,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Select', 'obsidian-forms')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            className: "is-secondary",
            onClick: handleCopyExistingForm,
            disabled: !selectedForm || isLoadingForms,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Copy', 'obsidian-forms')
          })]
        })]
      })]
    })
  });
}

/***/ }),

/***/ "./blocks/src/form/index.js":
/*!**********************************!*\
  !*** ./blocks/src/form/index.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./blocks/src/form/edit.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./style.scss */ "./blocks/src/form/style.scss");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./editor.scss */ "./blocks/src/form/editor.scss");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./block.json */ "./blocks/src/form/block.json");





(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_4__.name, {
  ..._block_json__WEBPACK_IMPORTED_MODULE_4__,
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: () => null
});

/***/ }),

/***/ "./blocks/src/form/editor.scss":
/*!*************************************!*\
  !*** ./blocks/src/form/editor.scss ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./blocks/src/form/style.scss":
/*!************************************!*\
  !*** ./blocks/src/form/style.scss ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/core-data":
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["coreData"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "./blocks/src/form/block.json":
/*!************************************!*\
  !*** ./blocks/src/form/block.json ***!
  \************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"obsidian-form/form","version":"0.1.0","title":"Obsidian Form","category":"widgets","icon":"feedback","description":"An Obsidian Form block.","supports":{"html":false,"align":true,"color":{"background":true,"text":true},"anchor":true},"attributes":{"anchor":{"type":"string","default":""},"formPostId":{"type":"number","default":0},"formSettings":{"type":"object","default":{"id":{"label":"Form ID","type":"string","value":""},"title":{"label":"Form Title","type":"string","value":""},"className":{"label":"Form Class","type":"string","value":""},"description":{"label":"Form Description","type":"string","value":""},"labelPlacement":{"label":"Field Label Placement","type":"select","value":"top","options":[{"label":"Top","value":"top"},{"label":"Left","value":"left"},{"label":"Bottom","value":"bottom"}]},"descriptionPlacement":{"label":"Field Description Placement","type":"radio","value":"bottom","options":[{"label":"Top","value":"top"},{"label":"Bottom","value":"bottom"}]},"globalHasPlaceholder":{"label":"Fields Have Placeholders","type":"toggle","value":true},"validationPlacement":{"label":"Validation Placement","type":"radio","value":"bottom","options":[{"label":"Top","value":"top"},{"label":"Bottom","value":"bottom"}]},"requiredIndicator":{"label":"Required Indicator","type":"string","value":"*"}}}},"editorScript":"file:./index.js","editorStyle":"file:./index.css","script":"file:./view.js","style":"file:./style-index.css","allowedBlocks":["obsidian-form/field-group"],"providesContext":{"obsidian-form/formSettings":"formSettings"},"render":"file:./index.php"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"form/index": 0,
/******/ 			"form/style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkobsidian_forms"] = self["webpackChunkobsidian_forms"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["form/style-index"], () => (__webpack_require__("./blocks/src/form/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map