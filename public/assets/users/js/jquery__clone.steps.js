/*! 
 * jQuery Steps v1.1.0 - 09/04/2014
 * Copyright (c) 2014 Rafael Staib (http://www.jquery-steps.com)
 * Licensed under MIT http://www.opensource.org/licenses/MIT
 */
;(function ($, undefined)
{
$.fn.extend({
    _aria: function (name, value)
    {
        return this.attr("aria-" + name, value);
    },

    _removeAria: function (name)
    {
        return this.removeAttr("aria-" + name);
    },

    _enableAria: function (enable)
    {
        return (enable == null || enable) ? 
            this.removeClass("disabled")._aria("disabled", "false") : 
            this.addClass("disabled")._aria("disabled", "true");
    },

    _showAria: function (show)
    {
        return (show == null || show) ? 
            this.show()._aria("hidden", "false") : 
            this.hide()._aria("hidden", "true");
    },

    _selectAria: function (select)
    {
        return (select == null || select) ? 
            this.addClass("current")._aria("selected", "true") : 
            this.removeClass("current")._aria("selected", "false");
    },

    _id: function (id)
    {
        return (id) ? this.attr("id", id) : this.attr("id");
    }
});

if (!String.prototype.format)
{
    String.prototype.format = function()
    {
        var args = (arguments.length === 1 && $.isArray(arguments[0])) ? arguments[0] : arguments;
        var formattedString = this;
        for (var i = 0; i < args.length; i++)
        {
            var pattern = new RegExp("\\{" + i + "\\}", "gm");
            formattedString = formattedString.replace(pattern, args[i]);
        }
        return formattedString;
    };
}

/**
 * A global unique id count.
 *
 * @static
 * @private
 * @property _uniqueId
 * @type Integer
 **/
var _uniqueId = 0;

/**
 * The plugin prefix for cookies.
 *
 * @final
 * @private
 * @property _cookiePrefix
 * @type String
 **/
var _cookiePrefix = "jQu3ry_5teps_St@te_";

/**
 * Suffix for the unique tab id.
 *
 * @final
 * @private
 * @property _tabSuffix
 * @type String
 * @since 0.9.7
 **/
var _tabSuffix = "-t-";

/**
 * Suffix for the unique tabpanel id.
 *
 * @final
 * @private
 * @property _tabpanelSuffix
 * @type String
 * @since 0.9.7
 **/
var _tabpanelSuffix = "-p-";

/**
 * Suffix for the unique title id.
 *
 * @final
 * @private
 * @property _titleSuffix
 * @type String
 * @since 0.9.7
 **/
var _titleSuffix = "-h-";

/**
 * An error message for an "index out of range" error.
 *
 * @final
 * @private
 * @property _indexOutOfRangeErrorMessage
 * @type String
 **/
var _indexOutOfRangeErrorMessage = "Index out of range.";

/**
 * An error message for an "missing corresponding element" error.
 *
 * @final
 * @private
 * @property _missingCorrespondingElementErrorMessage
 * @type String
 **/
var _missingCorrespondingElementErrorMessage = "One or more corresponding step {0} are missing.";

/**
 * Adds a step to the cache.
 *
 * @static
 * @private
 * @method addStepToCache
 * @param wizard {Object} A jQuery wizard object
 * @param step {Object} The step object to add
 **/

function addStepToCache(wizard, step)
{
    getSteps(wizard).push(step);
}

function analyzeData(wizard, options, state)
{
    var stepTitles = wizard.children(options.headerTag),
        stepContents = wizard.children(options.bodyTag);

    // Validate content
    if (stepTitles.length > stepContents.length)
    {
        throwError(_missingCorrespondingElementErrorMessage, "contents");
    }
    else if (stepTitles.length < stepContents.length)
    {
        throwError(_missingCorrespondingElementErrorMessage, "titles");
    }
        
    var startIndex = options.startIndex;

    state.stepCount = stepTitles.length;

    // Tries to load the saved state (step position)
    if (options.saveState && $.cookie)
    {
        var savedState = $.cookie(_cookiePrefix + getUniqueId(wizard));
        // Sets the saved position to the start index if not undefined or out of range 
        var savedIndex = parseInt(savedState, 0);
        if (!isNaN(savedIndex) && savedIndex < state.stepCount)
        {
            startIndex = savedIndex;
        }
    }

    state.currentIndex = startIndex;

    stepTitles.each(function (index)
    {
        var item = $(this), // item == header
            content = stepContents.eq(index),
            modeData = content.data("mode"),
            mode = (modeData == null) ? contentMode.html : getValidEnumValue(contentMode,
                (/^\s*$/.test(modeData) || isNaN(modeData)) ? modeData : parseInt(modeData, 0)),
            contentUrl = (mode === contentMode.html || content.data("url") === undefined) ?
                "" : content.data("url"),
            contentLoaded = (mode !== contentMode.html && content.data("loaded") === "1"),
            step = $.extend({}, stepModel, {
                title: item.html(),
                content: (mode === contentMode.html) ? content.html() : "",
                contentUrl: contentUrl,
                contentMode: mode,
                contentLoaded: contentLoaded
            });

        addStepToCache(wizard, step);
    });
}

/**
 * Triggers the onCanceled event.
 *
 * @static
 * @private
 * @method cancel
 * @param wizard {Object} The jQuery wizard object
 **/
function cancel(wizard)
{
    wizard.triggerHandler("canceled");
}

function decreaseCurrentIndexBy(state, decreaseBy)
{
    return state.currentIndex - decreaseBy;
}

/**
 * Removes the control functionality completely and transforms the current state to the initial HTML structure.
 *
 * @static
 * @private
 * @method destroy
 * @param wizard {Object} A jQuery wizard object
 **/
function destroy(wizard, options)
{
    var eventNamespace = getEventNamespace(wizard);

    // Remove virtual data objects from the wizard
    wizard.unbind(eventNamespace).removeData("uid").removeData("options")
        .removeData("state").removeData("steps").removeData("eventNamespace")
        .find(".actions a").unbind(eventNamespace);

    // Remove attributes and CSS classes from the wizard
    wizard.removeClass(options.clearFixCssClass + " vertical");

    var contents = wizard.find(".content > *");

    // Remove virtual data objects from panels and their titles
    contents.removeData("loaded").removeData("mode").removeData("url");

    // Remove attributes, CSS classes and reset inline styles on all panels and their titles
    contents.removeAttr("id").removeAttr("role").removeAttr("tabindex")
        .removeAttr("class").removeAttr("style")._removeAria("labelledby")
        ._removeAria("hidden");

    // Empty panels if the mode is set to 'async' or 'iframe'
    wizard.find(".content > [data-mode='async'],.content > [data-mode='iframe']").empty();

    var wizardSubstitute = $("<{0} class=\"{1}\"></{0}>".format(wizard.get(0).tagName, wizard.attr("class")));

    var wizardId = wizard._id();
    if (wizardId != null && wizardId !== "")
    {
        wizardSubstitute._id(wizardId);
    }

    wizardSubstitute.html(wizard.find(".content").html());
    wizard.after(wizardSubstitute);
    wizard.remove();

    return wizardSubstitute;
}

/**
 * Triggers the onFinishing and onFinished event.
 *
 * @static
 * @private
 * @method finishStep
 * @param wizard {Object} The jQuery wizard object
 * @param state {Object} The state container of the current wizard
 **/
function finishStep(wizard, state)
{
    var currentStep = wizard.find(".steps li").eq(state.currentIndex);

    if (wizard.triggerHandler("finishing", [state.currentIndex]))
    {
        currentStep.addClass("done").removeClass("error");
        wizard.triggerHandler("finished", [state.currentIndex]);
    }
    else
    {
        currentStep.addClass("error");
    }
}

/**
 * Gets or creates if not exist an unique event namespace for the given wizard instance.
 *
 * @static
 * @private
 * @method getEventNamespace
 * @param wizard {Object} A jQuery wizard object
 * @return {String} Returns the unique event namespace for the given wizard
 */
function getEventNamespace(wizard)
{
    var eventNamespace = wizard.data("eventNamespace");

    if (eventNamespace == null)
    {
        eventNamespace = "." + getUniqueId(wizard);
        wizard.data("eventNamespace", eventNamespace);
    }

    return eventNamespace;
}

function getStepAnchor(wizard, index)
{
    var uniqueId = getUniqueId(wizard);

    return wizard.find("#" + uniqueId + _tabSuffix + index);
}

function getStepPanel(wizard, index)
{
    var uniqueId = getUniqueId(wizard);

    return wizard.find("#" + uniqueId + _tabpanelSuffix + index);
}

function getStepTitle(wizard, index)
{
    var uniqueId = getUniqueId(wizard);

    return wizard.find("#" + uniqueId + _titleSuffix + index);
}

function getOptions(wizard)
{
    return wizard.data("options");
}

function getState(wizard)
{
    return wizard.data("state");
}

function getSteps(wizard)
{
    return wizard.data("steps");
}

/**
 * Gets a specific step object by index.
 *
 * @static
 * @private
 * @method getStep
 * @param index {Integer} An integer that belongs to the position of a step
 * @return {Object} A specific step object
 **/
function getStep(wizard, index)
{
    var steps = getSteps(wizard);

    if (index < 0 || index >= steps.length)
    {
        throwError(_indexOutOfRangeErrorMessage);
    }

    return steps[index];
}

/**
 * Gets or creates if not exist an unique id from the given wizard instance.
 *
 * @static
 * @private
 * @method getUniqueId
 * @param wizard {Object} A jQuery wizard object
 * @return {String} Returns the unique id for the given wizard
 */
function getUniqueId(wizard)
{
    var uniqueId = wizard.data("uid");

    if (uniqueId == null)
    {
        uniqueId = wizard._id();
        if (uniqueId == null)
        {
            uniqueId = "steps-uid-".concat(_uniqueId);
            wizard._id(uniqueId);
        }

        _uniqueId++;
        wizard.data("uid", uniqueId);
    }

    return uniqueId;
}

/**
 * Gets a valid enum value by checking a specific enum key or value.
 * 
 * @static
 * @private
 * @method getValidEnumValue
 * @param enumType {Object} Type of enum
 * @param keyOrValue {Object} Key as `String` or value as `Integer` to check for
 */
function getValidEnumValue(enumType, keyOrValue)
{
    validateArgument("enumType", enumType);
    validateArgument("keyOrValue", keyOrValue);

    // Is key
    if (typeof keyOrValue === "string")
    {
        var value = enumType[keyOrValue];
        if (value === undefined)
        {
            throwError("The enum key '{0}' does not exist.", keyOrValue);
        }

        return value;
    }
    // Is value
    else if (typeof keyOrValue === "number")
    {
        for (var key in enumType)
        {
            if (enumType[key] === keyOrValue)
            {
                return keyOrValue;
            }
        }

        throwError("Invalid enum value '{0}'.", keyOrValue);
    }
    // Type is not supported
    else
    {
        throwError("Invalid key or value type.");
    }
}

/**
 * Routes to the next step.
 *
 * @static
 * @private
 * @method goToNextStep
 * @param wizard {Object} The jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @return {Boolean} Indicates whether the action executed
 **/
function goToNextStep(wizard, options, state)
{
    return paginationClick(wizard, options, state, increaseCurrentIndexBy(state, 1));
}

/**
 * Routes to the previous step.
 *
 * @static
 * @private
 * @method goToPreviousStep
 * @param wizard {Object} The jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @return {Boolean} Indicates whether the action executed
 **/
function goToPreviousStep(wizard, options, state)
{
    return paginationClick(wizard, options, state, decreaseCurrentIndexBy(state, 1));
}

/**
 * Routes to a specific step by a given index.
 *
 * @static
 * @private
 * @method goToStep
 * @param wizard {Object} The jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param index {Integer} The position (zero-based) to route to
 * @return {Boolean} Indicates whether the action succeeded or failed
 **/
function goToStep(wizard, options, state, index)
{
    if (index < 0 || index >= state.stepCount)
    {
        throwError(_indexOutOfRangeErrorMessage);
    }

    if (options.forceMoveForward && index < state.currentIndex)
    {
        return;
    }

    var oldIndex = state.currentIndex;
    if (wizard.triggerHandler("stepChanging", [state.currentIndex, index]))
    {
        // Save new state
        state.currentIndex = index;
        saveCurrentStateToCookie(wizard, options, state);

        // Change visualisation
        refreshStepNavigation(wizard, options, state, oldIndex);
        refreshPagination(wizard, options, state);
        loadAsyncContent(wizard, options, state);
        startTransitionEffect(wizard, options, state, index, oldIndex, function()
        {
            wizard.triggerHandler("stepChanged", [index, oldIndex]);
        });
    }
    else
    {
        wizard.find(".steps li").eq(oldIndex).addClass("error");
    }

    return true;
}

function increaseCurrentIndexBy(state, increaseBy)
{
    return state.currentIndex + increaseBy;
}

/**
 * Initializes the component.
 *
 * @static
 * @private
 * @method initialize
 * @param options {Object} The component settings
 **/
function initialize(options)
{
    /*jshint -W040 */
    var opts = $.extend(true, {}, defaults, options);

    return this.each(function ()
    {
        var wizard = $(this);
        var state = {
            currentIndex: opts.startIndex,
            currentStep: null,
            stepCount: 0,
            transitionElement: null
        };

        // Create data container
        wizard.data("options", opts);
        wizard.data("state", state);
        wizard.data("steps", []);

        analyzeData(wizard, opts, state);
        render(wizard, opts, state);
        registerEvents(wizard, opts);

        // Trigger focus
        if (opts.autoFocus && _uniqueId === 0)
        {
            getStepAnchor(wizard, opts.startIndex).focus();
        }

        wizard.triggerHandler("init", [opts.startIndex]);
    });
}

/**
 * Inserts a new step to a specific position.
 *
 * @static
 * @private
 * @method insertStep
 * @param wizard {Object} The jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param index {Integer} The position (zero-based) to add
 * @param step {Object} The step object to add
 * @example
 *     $("#wizard").steps().insert(0, {
 *         title: "Title",
 *         content: "", // optional
 *         contentMode: "async", // optional
 *         contentUrl: "/Content/Step/1" // optional
 *     });
 * @chainable
 **/
function insertStep(wizard, options, state, index, step)
{
    if (index < 0 || index > state.stepCount)
    {
        throwError(_indexOutOfRangeErrorMessage);
    }

    // TODO: Validate step object

    // Change data
    step = $.extend({}, stepModel, step);
    insertStepToCache(wizard, index, step);
    if (state.currentIndex !== state.stepCount && state.currentIndex >= index)
    {
        state.currentIndex++;
        saveCurrentStateToCookie(wizard, options, state);
    }
    state.stepCount++;

    var contentContainer = wizard.find(".content"),
        header = $("<{0}>{1}</{0}>".format(options.headerTag, step.title)),
        body = $("<{0}></{0}>".format(options.bodyTag));

    if (step.contentMode == null || step.contentMode === contentMode.html)
    {
        body.html(step.content);
    }

    if (index === 0)
    {
        contentContainer.prepend(body).prepend(header);
    }
    else
    {
        getStepPanel(wizard, (index - 1)).after(body).after(header);
    }

    renderBody(wizard, state, body, index);
    renderTitle(wizard, options, state, header, index);
    refreshSteps(wizard, options, state, index);
    if (index === state.currentIndex)
    {
        refreshStepNavigation(wizard, options, state);
    }
    refreshPagination(wizard, options, state);

    return wizard;
}

/**
 * Inserts a step object to the cache at a specific position.
 *
 * @static
 * @private
 * @method insertStepToCache
 * @param wizard {Object} A jQuery wizard object
 * @param index {Integer} The position (zero-based) to add
 * @param step {Object} The step object to add
 **/
function insertStepToCache(wizard, index, step)
{
    getSteps(wizard).splice(index, 0, step);
}

/**
 * Handles the keyup DOM event for pagination.
 *
 * @static
 * @private
 * @event keyup
 * @param event {Object} An event object
 */
function keyUpHandler(event)
{
    var wizard = $(this),
        options = getOptions(wizard),
        state = getState(wizard);

    if (options.suppressPaginationOnFocus && wizard.find(":focus").is(":input"))
    {
        event.preventDefault();
        return false;
    }

    var keyCodes = { left: 37, right: 39 };
    if (event.keyCode === keyCodes.left)
    {
        event.preventDefault();
        goToPreviousStep(wizard, options, state);
    }
    else if (event.keyCode === keyCodes.right)
    {
        event.preventDefault();
        goToNextStep(wizard, options, state);
    }
}

/**
 * Loads and includes async content.
 *
 * @static
 * @private
 * @method loadAsyncContent
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 */
function loadAsyncContent(wizard, options, state)
{
    if (state.stepCount > 0)
    {
        var currentIndex = state.currentIndex,
            currentStep = getStep(wizard, currentIndex);

        if (!options.enableContentCache || !currentStep.contentLoaded)
        {
            switch (getValidEnumValue(contentMode, currentStep.contentMode))
            {
                case contentMode.iframe:
                    wizard.find(".content > .body").eq(state.currentIndex).empty()
                        .html("<iframe src=\"" + currentStep.contentUrl + "\" frameborder=\"0\" scrolling=\"no\" />")
                        .data("loaded", "1");
                    break;

                case contentMode.async:
                    var currentStepContent = getStepPanel(wizard, currentIndex)._aria("busy", "true")
                        .empty().append(renderTemplate(options.loadingTemplate, { text: options.labels.loading }));

                    $.ajax({ url: currentStep.contentUrl, cache: false }).done(function (data)
                    {
                        currentStepContent.empty().html(data)._aria("busy", "false").data("loaded", "1");
                        wizard.triggerHandler("contentLoaded", [currentIndex]);
                    });
                    break;
            }
        }
    }
}

/**
 * Fires the action next or previous click event.
 *
 * @static
 * @private
 * @method paginationClick
 * @param wizard {Object} The jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param index {Integer} The position (zero-based) to route to
 * @return {Boolean} Indicates whether the event fired successfully or not
 **/
function paginationClick(wizard, options, state, index)
{
    var oldIndex = state.currentIndex;

    if (index >= 0 && index < state.stepCount && !(options.forceMoveForward && index < state.currentIndex))
    {
        var anchor = getStepAnchor(wizard, index),
            parent = anchor.parent(),
            isDisabled = parent.hasClass("disabled");

        // Enable the step to make the anchor clickable!
        parent._enableAria();
        anchor.click();

        // An error occured
        if (oldIndex === state.currentIndex && isDisabled)
        {
            // Disable the step again if current index has not changed; prevents click action.
            parent._enableAria(false);
            return false;
        }

        return true;
    }

    return false;
}

/**
 * Fires when a pagination click happens.
 *
 * @static
 * @private
 * @event click
 * @param event {Object} An event object
 */
function paginationClickHandler(event)
{
    event.preventDefault();

    var anchor = $(this),
        wizard = anchor.parent().parent().parent().parent(),
        options = getOptions(wizard),
        state = getState(wizard),
        href = anchor.attr("href");

        var bus_id  = $("#bus_id").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        var get__main__type = $('#get__main__type').val();

    switch (href.substring(href.lastIndexOf("#") + 1))
    {
        case "cancel":
            cancel(wizard);
            break;

        case "finish":

            var form_data = new FormData();
            var terms_title = $(".terms_title").val();

            // add custom terms start ====
            var cooling_off_period = $(".cooling_off_period").val();
            var cancellation_policy_1 = $(".cancellation_policy_1").val();
            var cancellation_policy_2 = $(".cancellation_policy_2").val();
            var refund_1 = $(".refund_1").val();
            var refund_2 = $(".refund_2").val();
            // add custom terms end ====

            form_data.append("terms_title",terms_title );

            // add custom terms start ====
            form_data.append("cooling_off_period",cooling_off_period);
            form_data.append("cancellation_policy_1",cancellation_policy_1);
            form_data.append("cancellation_policy_2",cancellation_policy_2);
            form_data.append("refund_1",refund_1);
            form_data.append("refund_2",refund_2);
            // add custom terms end ====

            form_data.append("_token", CSRF_TOKEN);
            form_data.append("step", state.currentIndex);
            form_data.append("bus_id", bus_id);
            ajax_function(form_data,"last", state.currentIndex);

            finishStep(wizard, state);
            break;

        case "next":

            console.log("state.currentIndex ", state.currentIndex);
            console.log("get__main__type ", get__main__type);

            if(state.currentIndex == 0){
                if($("#hd_pf_logo").val() == ''){
                    if ($('#pf_logo')[0].files.length > 0) {
                        var file = $('#pf_logo')[0].files[0];
                        var file_size = file.size;
                        var file_type = file.type.toLowerCase();
                        var allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'];
                        if (file_size > 5000000) {
                            Swal.fire('Cancelled', 'Please upload an image that is less than 5 MB in size.', 'error');
                            return false;
                        } else if (allowed_types.indexOf(file_type) === -1) {
                            Swal.fire('Cancelled', 'Please upload an image of type: JPEG, PNG, JPG, GIF, SVG, or WebP.', 'error');
                            return false;
                        } else {
                            $("#logo-error").html("");
                        }
                    }
                }
                if( $('#i_deliver_online_sessions').val() == 'false' && $('#i_travel_to_you').val() == 'false' && $('#you_travel_to_me').val() == 'false' && $('#i_send_products_to_you').val() == 'false' ){
                    Swal.fire('Cancelled', 'Please select one of the box that describes your business', 'error');
                    return false;
                }   
                var file_data = $('#pf_logo').prop('files')[0];
                var hd_pf_logo = $("#hd_pf_logo").val();
                var description = $(".textarea_cstm_foerm").val();
                var pf_contact_name = $("#pf_contact_name").val();
                var fb_link = $("#fb_link").val();
                var insta_link = $("#insta_link").val();
                var in_link = $("#in_link").val();
                var i_deliver_online_sessions = $("#i_deliver_online_sessions").val();
                var i_travel_to_you = $("#i_travel_to_you").val();
                var you_travel_to_me = $("#you_travel_to_me").val();
                var i_send_products_to_you = $("#i_send_products_to_you").val();
                var form_data = new FormData();
                if(file_data == undefined || file_data == '' || file_data == null) {
                    form_data.append("pf_logo",'' );
                }else{
                    form_data.append("pf_logo",file_data );
                }
                form_data.append("hd_pf_logo",hd_pf_logo );
                form_data.append("description",description );
                form_data.append("pf_contact_name",pf_contact_name );
                form_data.append("fb_link",fb_link );
                form_data.append("insta_link",insta_link );
                form_data.append("in_link",in_link );
                form_data.append("i_deliver_online_sessions",i_deliver_online_sessions );
                form_data.append("i_travel_to_you",i_travel_to_you );
                form_data.append("you_travel_to_me",you_travel_to_me );
                form_data.append("i_send_products_to_you",i_send_products_to_you );
                form_data.append("step", 0);
                form_data.append("_token", CSRF_TOKEN);
                form_data.append("bus_id", bus_id);
                ajax_function(form_data, "first", 0);
            }

            if(state.currentIndex == 1){
                var form_data = new FormData();
                var this_name = false;
                var this_type = false;
                var this_hours = false;
                var this_price = false;
                $(document).find('.ser_name').each(function(i) {
                    var ser_name = $(this).find('input[name="service_name['+i+'][name]"]').val();
                    var ser_length = $(this).find('input[name="service_name['+i+'][length]"]').val();
                    var ser_price = $(this).find('input[name="service_name1['+i+'][cost]"]').val();
                    var ser_price2 = $(this).find('input[name="service_name2['+i+'][cost]"]').val();
                    var service_desc = $(this).find('input[name="service_name['+i+'][service_desc]"]').val();
                    var service_type = $(this).find('select[name="service_name['+i+'][service_type]"]').val(); 
                    var service__cat = $(this).find('select[name="service_name['+i+'][service__cat]"]').val(); 
                    var service_hours = $(this).find('select[name="service_name['+i+'][hours]"]').val(); 
                    var service_minutes = $(this).find('select[name="service_name['+i+'][minutes]"]').val(); 
                    var product_image = $(this).find('input[name="service_name['+i+'][product_image]"]')[0].files[0]; 
                    
                    if(ser_name === "" || ser_name === 0 ){
                      this_name = true;
                    }

                    if(service_type === ""){
                        this_type = true;
                    }

                    if(service_hours === "" || service_hours === "00"){
                        if(service_minutes === "" || service_minutes === "00"){
                           this_hours = true;
                        }else{
                           this_hours = false;
                        }
                    }

                    if(ser_price === "" || ser_price === 0 ){
                        this_price = true;
                    }
                    form_data.append("service_name["+i+"][name]", ser_name);
                    form_data.append("service_name["+i+"][length]", ser_length);
                    form_data.append("service_name["+i+"][cost]", ser_price);
                    form_data.append("service_name["+i+"][cost2]", ser_price2);
                    form_data.append("service_name["+i+"][service_desc]", service_desc);
                    form_data.append("service_name["+i+"][service_type]", service_type);
                    form_data.append("service_name["+i+"][service__cat]", service__cat);
                    form_data.append("service_name["+i+"][service_hours]", service_hours);
                    form_data.append("service_name["+i+"][service_minutes]", service_minutes);
                    if (product_image) {
                        form_data.append("service_name[" + i + "][product_image]", product_image);
                    }
                })
                form_data.append("_token", CSRF_TOKEN);
                form_data.append("step", 1);
                form_data.append("bus_id", bus_id);

                if(this_name === true){
                    Swal.fire('Cancelled','Please fill service name','error')
                    return false;
                }
                if(this_price === true){
                    Swal.fire('Cancelled','Please fill service price','error')
                    return false;
                }
                if(this_type === true){
                    Swal.fire('Cancelled','Please fill service type','error')
                    return false;
                }
                if(this_hours === true){
                    Swal.fire('Cancelled','Please fill service duration','error')
                    return false;
                }
                ajax_function(form_data, '' , 1);
            }

            if(get__main__type == "product"){

                if(state.currentIndex == 2){
                    var isValidimg = false;
                    const totalImages = $("input[name*='gallery_images']")[0].files.length;
                    var gallery_img_input = $(".gallery_img_input").val();
                    if(gallery_img_input){
                        isValidimg = true;
                    }else{
                        if(totalImages == 0){
                            isValidimg = false;
                        }else{
                            isValidimg = true;
                        }
                    }
                    if(isValidimg==false){
                        Swal.fire('Cancelled','Please upload image','error')
                        return false;
                    }
                    var form_data = new FormData();
                    let images = $("input[name*='gallery_images']")[0];
                    for (let i = 0; i < totalImages; i++) {
                        form_data.append('gallery_images['+i+']', images.files[i]);
                    }
                    form_data.append('totalImages', totalImages);
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 4);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, "gallery"  , 4);
                }

                if(state.currentIndex == 3){
                    if($('#zoom_selected').is(':checked')){
                        var zoom_selected = $("#zoom_selected").val();
                    }else{
                        var zoom_selected = '';
                    }
                    var search_keyword = $(".search_keyword").val();
                    if(search_keyword==''){
                        Swal.fire('Cancelled','Please add some search keywords','error')
                        return false;
                    }
                    var form_data = new FormData();
                    form_data.append("search_keyword",search_keyword );
                    form_data.append("zoom_selected",zoom_selected );
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 6);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, '' , 6);
                }
    
                if(state.currentIndex == 4){
                    var form_data = new FormData();
                    var terms_title = $(".terms_title").val();
                    // add custom terms start ====
                    var cooling_off_period = $(".cooling_off_period").val();
                    var cancellation_policy_1 = $(".cancellation_policy_1").val();
                    var cancellation_policy_2 = $(".cancellation_policy_2").val();
                    var refund_1 = $(".refund_1").val();
                    var refund_2 = $(".refund_2").val();
                    // add custom terms end ====
                    form_data.append("terms_title",terms_title );
                    // add custom terms start ====
                    form_data.append("cooling_off_period",cooling_off_period);
                    form_data.append("cancellation_policy_1",cancellation_policy_1);
                    form_data.append("cancellation_policy_2",cancellation_policy_2);
                    form_data.append("refund_1",refund_1);
                    form_data.append("refund_2",refund_2);
                    // add custom terms end ====
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 7);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, '' , 7);
                    
                }

            }else{

                if(state.currentIndex == 2){
                    var count = $(".custom_checkboxs[type='checkbox']:checked").length;
                    if(count > 0){
                        var form_data = new FormData();
                        $('.custom_checkboxs:checkbox:checked').each(function(i){
                            var facilities = $(this).attr('name');
                            form_data.append(facilities,'on' );
                        });
                        form_data.append("_token", CSRF_TOKEN);
                        form_data.append("bus_id", bus_id);
                        form_data.append("step", 2);
                        ajax_function(form_data, '' , 2);
                    }else{
                        Swal.fire('Cancelled','Please select atleast one option','error')
                        return false;
                    }
                }
    
                if(state.currentIndex == 3){
                    var count = $(".cstmtime_slots input[type='checkbox']:checked").length;
                    if(count > 0){
                        var form_data = new FormData();
                        var arr = [];
                        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        var start_time__ = false;
                        var end_time__ = false;
                        var start_time_count = 0; 
                        var isValidend = false; 
                        $.each(days, function(i, day) {
                            var hop = 0
                            $('.sunday_div:visible input[name="start_srv_timesloat['+day+'][]"]').each(function(x){
                                var start_time = $(this).val();
                                var end_time = $('input[name="end_srv_timesloat['+day+'][]"]').eq(hop).val();
                                if (start_time >= end_time) {
                                    isValidend = true;
                                }
                                if(start_time === 0 || start_time === "" ){
                                    start_time__ = true;                          
                                }
                                hop++
                                start_time_count++;
                                form_data.append("start_srv_timesloat["+day+"]["+x+"]",start_time );
                            })
                            $('.sunday_div:visible input[name="end_srv_timesloat['+day+'][]"]').each(function(x){
                                var end_time = $(this).val();
                                if(end_time === 0 || end_time === "" ){
                                    end_time__ = true;                          
                                }
                                form_data.append("end_srv_timesloat["+day+"]["+x+"]",end_time );
                            })
                        })
                        form_data.append("start_time_count",start_time_count );
                        $('.custom_checkbox:checkbox:checked').each(function(i){
                            var set_services_selected_day = $(this).attr('name');
                            form_data.append(set_services_selected_day,'on' );
                        })
                        var weeks = [];
                        $('.custom_checkbox:checkbox:checked').each(function(i){
                            var week = $(this).next('.spantexts').html();
                            weeks.push(week);;
                        })
                        var weeksString = weeks.join(',');
                        form_data.append('weeks', weeksString);
                        if(start_time__){
                            Swal.fire('Cancelled','Please select start time','error')
                            return false;
                        }
                        if(end_time__){
                            Swal.fire('Cancelled','Please select end time','error')
                            return false;
                        }
                        if(isValidend){
                            Swal.fire('Cancelled','Start time must be less than end time.','error')
                            return false;
                        }
                        var overlap = false;
                        $('.sunday_div:visible').each(function(x) {
                           var arr_start = [];
                           var end_start = [];
                           var log_start = $(this).find('[name*="start_srv_timesloat"]');
                           var log_end = $(this).find('[name*="end_srv_timesloat"]');
                           console.log(log_start.length)
                            if (log_start.length > 1) {
                                for (let i = 0; i < log_start.length; i++) {
                                    var startValue1 = log_start.eq(i).val();
                                    var endValue1 = log_end.eq(i).val();                   
                                    arr_start.push(startValue1);
                                    end_start.push(endValue1);
                                }
                                for (var i = 0; i < arr_start.length; i++) {
                                    for (var j = 0; j < arr_start.length; j++) {
                                        if (i !== j && (
                                            (arr_start[i] >= arr_start[j] && arr_start[i] < end_start[j]) ||
                                            (arr_start[i] <= arr_start[j] && end_start[i] > arr_start[j])
                                        )) {
                                            overlap = true;
                                            break;
                                        }
                                    }
                                    if (overlap) {
                                        break;
                                    }
                                }
                            }
                        });
                        if (overlap) {
                            Swal.fire('Cancelled','Time overlap. Please adjust your time schedule.','error')
                            return false;
                        } 
                        form_data.append("_token", CSRF_TOKEN);
                        form_data.append("step", 3);
                        form_data.append("bus_id", bus_id);
                        ajax_function(form_data, '' , 3);
                    }else{
                        Swal.fire('Cancelled','Please select atleast one option','error')
                        return false;
                    }
                }
    
                if(state.currentIndex == 4){
                    var isValidimg = false;
                    const totalImages = $("input[name*='gallery_images']")[0].files.length;
                    var gallery_img_input = $(".gallery_img_input").val();
                    if(gallery_img_input){
                        isValidimg = true;
                    }else{
                        if(totalImages == 0){
                            isValidimg = false;
                        }else{
                            isValidimg = true;
                        }
                    }
                    if(isValidimg==false){
                        Swal.fire('Cancelled','Please upload image','error')
                        return false;
                    }
                    var form_data = new FormData();
                    let images = $("input[name*='gallery_images']")[0];
                    for (let i = 0; i < totalImages; i++) {
                        form_data.append('gallery_images['+i+']', images.files[i]);
                    }
                    form_data.append('totalImages', totalImages);
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 4);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, "gallery"  , 4);
                }
    
                if(state.currentIndex == 5){
                    var how_to_prepare = $(".how_to_prepare").val();
                    var how_to_get_there = $(".how_to_get_there").val();
                    var form_data = new FormData();
                    form_data.append("how_to_prepare",how_to_prepare );
                    form_data.append("how_to_get_there",how_to_get_there );
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 5);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, '' , 5);
                }
    
                if(state.currentIndex == 6){
                    if($('#zoom_selected').is(':checked')){
                        var zoom_selected = $("#zoom_selected").val();
                    }else{
                        var zoom_selected = '';
                    }
                    var search_keyword = $(".search_keyword").val();
                    if(search_keyword==''){
                        Swal.fire('Cancelled','Please add some search keywords','error')
                        return false;
                    }
                    var form_data = new FormData();
                    form_data.append("search_keyword",search_keyword );
                    form_data.append("zoom_selected",zoom_selected );
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 6);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, '' , 6);
                }
    
                if(state.currentIndex == 7){
                    var form_data = new FormData();
                    var terms_title = $(".terms_title").val();
                    // add custom terms start ====
                    var cooling_off_period = $(".cooling_off_period").val();
                    var cancellation_policy_1 = $(".cancellation_policy_1").val();
                    var cancellation_policy_2 = $(".cancellation_policy_2").val();
                    var refund_1 = $(".refund_1").val();
                    var refund_2 = $(".refund_2").val();
                    // add custom terms end ====
                    form_data.append("terms_title",terms_title );
                    // add custom terms start ====
                    form_data.append("cooling_off_period",cooling_off_period);
                    form_data.append("cancellation_policy_1",cancellation_policy_1);
                    form_data.append("cancellation_policy_2",cancellation_policy_2);
                    form_data.append("refund_1",refund_1);
                    form_data.append("refund_2",refund_2);
                    // add custom terms end ====
                    form_data.append("_token", CSRF_TOKEN);
                    form_data.append("step", 7);
                    form_data.append("bus_id", bus_id);
                    ajax_function(form_data, '' , 7);
                    
                }

            }




            
            function setProgressBar(curStep){
              
                percent = curStep;
                console.log(percent);
                $(".progress-bar")
                .css("width",percent+"%")
                .html(percent+"%");   
             }
      
            
            function ajax_function(form_data , extra = '',count_pr = ''){
                $.ajax({        
                    url:"/multistep_data__clone",
                    type: "POST",
                   
                    data: form_data,    
                    contentType: false,
                    cache: false,
                    processData: false,   
                    beforeSend: function() {
                        if(extra == "first"){
                            $("#step1").css("pointer-events", "none");   
                            $(".actions").css("pointer-events", "none");    
                        }                    
                    },
                    success:function(data){

                        if(count_pr == 0){
                            location.reload();
                        }

                        if(data){
                            var dataJsonResponse = JSON.parse(data);
                            if(dataJsonResponse.i_deliver_online_sessions__val != undefined){
                                console.log('response data for i_deliver_online_sessions__val --- ', dataJsonResponse.i_deliver_online_sessions__val);
                                if(dataJsonResponse.i_deliver_online_sessions__val == 'true'){
                                    $('#i_deliver_online_sessions').attr('data-value', 'true');
                                    $("#i_deliver_online_sessions").prop( "checked", true );
                                }
                            }
                            if(dataJsonResponse.i_travel_to_you__val != undefined){
                                console.log('response data for i_travel_to_you__val --- ', dataJsonResponse.i_travel_to_you__val);
                                if(dataJsonResponse.i_travel_to_you__val == 'true'){
                                    $('#i_travel_to_you').attr('data-value', 'true');
                                    $("#i_travel_to_you").prop( "checked", true );
                                }
                            }
                            if(dataJsonResponse.you_travel_to_me__val != undefined){
                                console.log('response data for you_travel_to_me__val --- ', dataJsonResponse.you_travel_to_me__val);
                                if(dataJsonResponse.you_travel_to_me__val == 'true'){
                                    $('#you_travel_to_me').attr('data-value', 'true');
                                    $("#you_travel_to_me").prop( "checked", true );
                                }
                            }
                            if(dataJsonResponse.i_send_products_to_you__val != undefined){
                                console.log('response data for i_send_products_to_you__val --- ', dataJsonResponse.i_send_products_to_you__val);
                                if(dataJsonResponse.i_send_products_to_you__val == 'true'){
                                    $('#i_send_products_to_you').attr('data-value', 'true');
                                    $("#i_send_products_to_you").prop( "checked", true );
                                }
                            }
                        }

                       
                        if(data){
                            // setProgressBar(count_pr);
                            if(extra == "gallery"){
                                $(".gallery-img").html(data);
                                // $(".uploaded").html("");
                                // $(".image-uploader").removeClass("has-files") 
                                $(".delete-image").trigger("click")
                            }

                            if(extra == "first"){
                                $("#bus_id").val(data);
                                $(".actions").removeAttr("style");
                                $("#step1").removeAttr("style");
                            }

                            if(extra == "last"){
                                setProgressBar(100)
                                location.reload();
                            }

                            $(".show-loader").remove();

                            

                            $("#success_msg").addClass('alert-success').html("profile updated")
                            setTimeout(function(){ $('#success_msg').removeClass('alert-success').html(""); }, 5000);
                        }
                    },
                    error: function(xhr, status, error) {
                        $(".actions").removeAttr("style");
                        $.each(xhr.responseJSON.errors, function (key, item) {
                           $("#success_msg").addClass('alert-danger').html(item)
                           setTimeout(function(){ $('#success_msg').removeClass('alert-danger').html(""); }, 5000);
                        });
                       return false;
                    }
                });
            }

            

            // if(state.currentIndex == 2){
            //     var validatetwo = validateStepTwo();
            //     if(validatetwo==false){
            //         return false;
            //     }
            // }

            // if(state.currentIndex == 3){
            //     var validatethree = validateStepThree();
            //     if(validatethree==false){
            //         return false;
            //     }
            // }

            // if(state.currentIndex == 4){
            //     var validatefour = validateStepFour();
            //     if(validatefour==false){
            //         return false;
            //     }
            // }
            // if(state.currentIndex == 5){
            //     var validatefive = validateStepSix();
            //     if(validatefive==false){
            //         return false;
            //     }
            // }

            
            goToNextStep(wizard, options, state);
            break;

        case "previous":
           
            
            goToPreviousStep(wizard, options, state);
            break;
    }
}
//     function validateStepOne(currentIndex) {
//       var isValid = true;
//       var file_data = $('#pf_logo').prop('files')[0];
//          var hd_pf_logo = $("#hd_pf_logo").val();
//          if(!$("#hd_pf_logo").val()){
//             if($('#pf_logo')[0].files.length == 0){
//                 $("#logo-error").html("This field is required"); 
//                isValid = false;
//             }else{
              
//                var file_size = $('#pf_logo')[0].files[0].size;
//                if(file_size>5000000) {
//                   $("#logo-error").html("File size is greater than 10MB");
//                   isValid = false;
//                }else{
//                   $('#logo-error').html('');
//                   isValid = true;
//                } 
//             }
//          } else{
//             $('#logo-error').html('');
//             isValid = true; 
//          }
        
//         return isValid;
//    }

//     function validateStepTwo(currentIndex) {
//         var isValidDay = false;
//         var selected_day=[];
//         $('.custom_checkbox:checkbox:checked').each(function(i){
//             var set_services_selected_day = $(this).attr('name');
//             selected_day.push(set_services_selected_day);
//             isValidDay = true;
//         })
//         var arr = [];
//         var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
//         var isValidstart = true;
//         var isValidend = true;

//         var isValidall = true;
//         var isValid3 = true;

//         if(isValidDay==true){
//         var previousEndTime = '00:00';
//          $.each(selected_day, function(i, day) {
          
//            var res = day.slice(-1);
//             var day1= days[res];
//             previousEndTime = '00:00';
//             $('input[name="start_srv_timesloat['+day1+'][]"]').each(function(x){
//                 var start_time = $(this).val();
//                 var end_time = $('input[name="end_srv_timesloat['+day1+'][]"]').eq(x).val();
//                 if(!start_time){
//                     isValidstart = false;  
//                 }
//                 if (previousEndTime !== null && start_time > previousEndTime) {
//                   previousEndTime = end_time;
//                }else{
//                   isValid3 = false;
//                }
//                previousEndTime = end_time;

//             })

//             $('input[name="end_srv_timesloat['+day1+'][]"]').each(function(x){
//                var end_time = $(this).val();
//                 var start_time = $('input[name="start_srv_timesloat['+day1+'][]"]').eq(x).val();

//                if (start_time >= end_time) {
//                   isValidend = false;
//                } 
//                   if(!end_time){
//                      isValidstart = false;  
//                   }
              
//             })
//          })

//          if(isValid3==false){
//             isValidall = false;
//              $('.startdate_error2').html('Invalid time block. Start and end times cannot overlap or be equal to previous blocks.');
//           }else{
//             $('.startdate_error2').html('');
//          }


//           if(isValidstart==false){
//             isValidall = false;
//              $('.startdate_error').html('All start date and end date fields are required.');
//              $('.startdate_error2').html('');
//           }else{
//             $('.startdate_error').html('');
//          }


//          if(isValidend==false){
//             isValidall = false;
//              $('.enddate_error').html('Start time must be less than end time. Form cannot be submitted.');
//           }else{
//             $('.enddate_error').html('');
//          }

//       }

//      return isValidall;
//   }


//   function validateStepThree(currentIndex) {
//       var isValidDay = false;
//       var isValidDay1 = false;
//       var isValidDay2 = true;

//       $('.ser_name').each(function(i){
//          var ser_name = $(this).find('input[name="service_name['+i+'][name]"]').val();
//          var ser_price = $(this).find('input[name="service_name['+i+'][cost]"]').val();
        
//          if(!ser_name){
//             isValidDay2 = false;
//             isValidDay = false; 
//          }
//          if(!ser_price){
//             isValidDay2 = false;
//             isValidDay1 = false;
//          }
//       })

//       if(isValidDay2==false){
//          $('.services_error').html('All fields are required.');
//       }else{
//          $('.services_error').html('');
//       }
//       console.log(isValidDay2)
//       return isValidDay2;
//   }

//   function validateStepFour(currentIndex) {
//       var isValidmobile = false;
//       var isValidfix = false;
//       var isValidloc = false;

//       var isValid =false;

//       if($('#mobile').is(':checked')){
//          isValidmobile = true;
//       }else{
//          isValidmobile = false;
//       }
//       if($('#fixed').is(':checked')){
//          isValidfix = true;
//       }else{
//          isValidfix = false;
//       }

//       if($('#online').is(':checked')){
//          isValidloc = true;
//       }else{
//          isValidloc = false;
//       }

//       if(isValidmobile == false && isValidfix == false && isValidloc == false){
//          isValid = false;
//       }else{
//          isValid = true;
//       }

//       if(isValid==false){
//          $('.box_error').html('Please select atleast one field.');
//       }else{
//          $('.box_error').html('');
//       }

//       return isValid;

//   }

//     function validateStepSix(currentIndex) { 
//       var isValidimg = false;
//        const totalImages = $("input[name*='gallery_images']")[0].files.length;
//       let images = $("input[name*='gallery_images']")[0];
//       if(totalImages == 0){
//          isValidimg = false;
//       }else{
//          isValidimg = true;
//       }
//       if(isValidimg==false){
//         $('.err_msgimg').html('Please select atleast one image.');
//       }else{
//         $('.err_msgimg').html('');
//       }

//         return isValidimg;
//     }



   
/**
 * Refreshs the visualization state for the entire pagination.
 *
 * @static
 * @private
 * @method refreshPagination
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 */
function refreshPagination(wizard, options, state)
{
    if (options.enablePagination)
    {
        var finish = wizard.find(".actions a[href$='#finish']").parent(),
            next = wizard.find(".actions a[href$='#next']").parent();

        if (!options.forceMoveForward)
        {
            var previous = wizard.find(".actions a[href$='#previous']").parent();
            previous._enableAria(state.currentIndex > 0);
        }

        if (options.enableFinishButton && options.showFinishButtonAlways)
        {
            finish._enableAria(state.stepCount > 0);
            next._enableAria(state.stepCount > 1 && state.stepCount > (state.currentIndex + 1));
        }
        else
        {
            finish._showAria(options.enableFinishButton && state.stepCount === (state.currentIndex + 1));
            next._showAria(state.stepCount === 0 || state.stepCount > (state.currentIndex + 1)).
                _enableAria(state.stepCount > (state.currentIndex + 1) || !options.enableFinishButton);
        }
    }
}

/**
 * Refreshs the visualization state for the step navigation (tabs).
 *
 * @static
 * @private
 * @method refreshStepNavigation
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param [oldIndex] {Integer} The index of the prior step
 */
function refreshStepNavigation(wizard, options, state, oldIndex)
{
    var currentOrNewStepAnchor = getStepAnchor(wizard, state.currentIndex),
        currentInfo = $("<span class=\"current-info audible\">" + options.labels.current + " </span>"),
        stepTitles = wizard.find(".content > .title");

    if (oldIndex != null)
    {
        var oldStepAnchor = getStepAnchor(wizard, oldIndex);
        oldStepAnchor.parent().addClass("done").removeClass("error")._selectAria(false);
        stepTitles.eq(oldIndex).removeClass("current").next(".body").removeClass("current");
        currentInfo = oldStepAnchor.find(".current-info");
        currentOrNewStepAnchor.focus();
    }

    currentOrNewStepAnchor.prepend(currentInfo).parent()._selectAria().removeClass("done")._enableAria();
    stepTitles.eq(state.currentIndex).addClass("current").next(".body").addClass("current");
}

/**
 * Refreshes step buttons and their related titles beyond a certain position.
 *
 * @static
 * @private
 * @method refreshSteps
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param index {Integer} The start point for refreshing ids
 */
function refreshSteps(wizard, options, state, index)
{
    var uniqueId = getUniqueId(wizard);

    for (var i = index; i < state.stepCount; i++)
    {
        var uniqueStepId = uniqueId + _tabSuffix + i,
            uniqueBodyId = uniqueId + _tabpanelSuffix + i,
            uniqueHeaderId = uniqueId + _titleSuffix + i,
            title = wizard.find(".title").eq(i)._id(uniqueHeaderId);

        wizard.find(".steps a").eq(i)._id(uniqueStepId)
            ._aria("controls", uniqueBodyId).attr("href", "#" + uniqueHeaderId)
            .html(renderTemplate(options.titleTemplate, { index: i + 1, title: title.html() }));
        wizard.find(".body").eq(i)._id(uniqueBodyId)
            ._aria("labelledby", uniqueHeaderId);
    }
}

function registerEvents(wizard, options)
{
    var eventNamespace = getEventNamespace(wizard);

    wizard.bind("canceled" + eventNamespace, options.onCanceled);
    wizard.bind("contentLoaded" + eventNamespace, options.onContentLoaded);
    wizard.bind("finishing" + eventNamespace, options.onFinishing);
    wizard.bind("finished" + eventNamespace, options.onFinished);
    wizard.bind("init" + eventNamespace, options.onInit);
    wizard.bind("stepChanging" + eventNamespace, options.onStepChanging);
    wizard.bind("stepChanged" + eventNamespace, options.onStepChanged);

    if (options.enableKeyNavigation)
    {
        wizard.bind("keyup" + eventNamespace, keyUpHandler);
    }

    wizard.find(".actions a").bind("click" + eventNamespace, paginationClickHandler);
}

/**
 * Removes a specific step by an given index.
 *
 * @static
 * @private
 * @method removeStep
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param index {Integer} The position (zero-based) of the step to remove
 * @return Indecates whether the item is removed.
 **/
function removeStep(wizard, options, state, index)
{
    // Index out of range and try deleting current item will return false.
    if (index < 0 || index >= state.stepCount || state.currentIndex === index)
    {
        return false;
    }

    // Change data
    removeStepFromCache(wizard, index);
    if (state.currentIndex > index)
    {
        state.currentIndex--;
        saveCurrentStateToCookie(wizard, options, state);
    }
    state.stepCount--;

    getStepTitle(wizard, index).remove();
    getStepPanel(wizard, index).remove();
    getStepAnchor(wizard, index).parent().remove();

    // Set the "first" class to the new first step button 
    if (index === 0)
    {
        wizard.find(".steps li").first().addClass("first");
    }

    // Set the "last" class to the new last step button 
    if (index === state.stepCount)
    {
        wizard.find(".steps li").eq(index).addClass("last");
    }

    refreshSteps(wizard, options, state, index);
    refreshPagination(wizard, options, state);

    return true;
}

function removeStepFromCache(wizard, index)
{
    getSteps(wizard).splice(index, 1);
}

/**
 * Transforms the base html structure to a more sensible html structure.
 *
 * @static
 * @private
 * @method render
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 **/
function render(wizard, options, state)
{
    // Create a content wrapper and copy HTML from the intial wizard structure
    var wrapperTemplate = "<{0} class=\"{1}\">{2}</{0}>",
        orientation = getValidEnumValue(stepsOrientation, options.stepsOrientation),
        verticalCssClass = (orientation === stepsOrientation.vertical) ? " vertical" : "",
        contentWrapper = $(wrapperTemplate.format(options.contentContainerTag, "content " + options.clearFixCssClass, wizard.html())),
        stepsWrapper = $(wrapperTemplate.format(options.stepsContainerTag, "steps " + options.clearFixCssClass, "<ul role=\"tablist\"></ul>")),
        stepTitles = contentWrapper.children(options.headerTag),
        stepContents = contentWrapper.children(options.bodyTag);

    // Transform the wizard wrapper and remove the inner HTML
    wizard.attr("role", "application").empty().append(stepsWrapper).append(contentWrapper)
        .addClass(options.cssClass + " " + options.clearFixCssClass + verticalCssClass);

    // Add WIA-ARIA support
    stepContents.each(function (index)
    {
        renderBody(wizard, state, $(this), index);
    });

    stepTitles.each(function (index)
    {
        renderTitle(wizard, options, state, $(this), index);
    });

    refreshStepNavigation(wizard, options, state);
    renderPagination(wizard, options, state);
}

/**
 * Transforms the body to a proper tabpanel.
 *
 * @static
 * @private
 * @method renderBody
 * @param wizard {Object} A jQuery wizard object
 * @param body {Object} A jQuery body object
 * @param index {Integer} The position of the body
 */
function renderBody(wizard, state, body, index)
{
    var uniqueId = getUniqueId(wizard),
        uniqueBodyId = uniqueId + _tabpanelSuffix + index,
        uniqueHeaderId = uniqueId + _titleSuffix + index;

    body._id(uniqueBodyId).attr("role", "tabpanel")._aria("labelledby", uniqueHeaderId)
        .addClass("body")._showAria(state.currentIndex === index);
}

/**
 * Renders a pagination if enabled.
 *
 * @static
 * @private
 * @method renderPagination
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 */
function renderPagination(wizard, options, state)
{
    if (options.enablePagination)
    {
        var pagination = "<{0} class=\"actions {1}\"><ul role=\"menu\" aria-label=\"{2}\">{3}</ul></{0}>",
            buttonTemplate = "<li><a href=\"#{0}\" role=\"menuitem\">{1}</a></li>",
            buttons = "";

        if (!options.forceMoveForward)
        {
            buttons += buttonTemplate.format("previous", options.labels.previous);
        }

        buttons += buttonTemplate.format("next", options.labels.next);

        if (options.enableFinishButton)
        {
            buttons += buttonTemplate.format("finish", options.labels.finish);
        }

        if (options.enableCancelButton)
        {
            buttons += buttonTemplate.format("cancel", options.labels.cancel);
        }

        wizard.append(pagination.format(options.actionContainerTag, options.clearFixCssClass,
            options.labels.pagination, buttons));

        refreshPagination(wizard, options, state);
        loadAsyncContent(wizard, options, state);
    }
}

/**
 * Renders a template and replaces all placeholder.
 *
 * @static
 * @private
 * @method renderTemplate
 * @param template {String} A template
 * @param substitutes {Object} A list of substitute
 * @return {String} The rendered template
 */
function renderTemplate(template, substitutes)
{
    var matches = template.match(/#([a-z]*)#/gi);

    for (var i = 0; i < matches.length; i++)
    {
        var match = matches[i], 
            key = match.substring(1, match.length - 1);

        if (substitutes[key] === undefined)
        {
            throwError("The key '{0}' does not exist in the substitute collection!", key);
        }

        template = template.replace(match, substitutes[key]);
    }

    return template;
}

/**
 * Transforms the title to a step item button.
 *
 * @static
 * @private
 * @method renderTitle
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 * @param header {Object} A jQuery header object
 * @param index {Integer} The position of the header
 */
function renderTitle(wizard, options, state, header, index)
{
    var uniqueId = getUniqueId(wizard),
        uniqueStepId = uniqueId + _tabSuffix + index,
        uniqueBodyId = uniqueId + _tabpanelSuffix + index,
        uniqueHeaderId = uniqueId + _titleSuffix + index,
        stepCollection = wizard.find(".steps > ul"),
        title = renderTemplate(options.titleTemplate, {
            index: index + 1,
            title: header.html()
        }),
        stepItem = $("<li role=\"tab\"><a id=\"" + uniqueStepId + "\" href=\"#" + uniqueHeaderId + 
            "\" aria-controls=\"" + uniqueBodyId + "\">" + title + "</a></li>");
        
    stepItem._enableAria(options.enableAllSteps || state.currentIndex > index);

    if (state.currentIndex > index)
    {
        stepItem.addClass("done");
    }

    header._id(uniqueHeaderId).attr("tabindex", "-1").addClass("title");

    if (index === 0)
    {
        stepCollection.prepend(stepItem);
    }
    else
    {
        stepCollection.find("li").eq(index - 1).after(stepItem);
    }

    // Set the "first" class to the new first step button
    if (index === 0)
    {
        stepCollection.find("li").removeClass("first").eq(index).addClass("first");
    }

    // Set the "last" class to the new last step button
    if (index === (state.stepCount - 1))
    {
        stepCollection.find("li").removeClass("last").eq(index).addClass("last");
    }

    // Register click event
    stepItem.children("a").bind("click" + getEventNamespace(wizard), stepClickHandler);
}

/**
 * Saves the current state to a cookie.
 *
 * @static
 * @private
 * @method saveCurrentStateToCookie
 * @param wizard {Object} A jQuery wizard object
 * @param options {Object} Settings of the current wizard
 * @param state {Object} The state container of the current wizard
 */
function saveCurrentStateToCookie(wizard, options, state)
{
    if (options.saveState && $.cookie)
    {
        $.cookie(_cookiePrefix + getUniqueId(wizard), state.currentIndex);
    }
}

function startTransitionEffect(wizard, options, state, index, oldIndex, doneCallback)
{
    var stepContents = wizard.find(".content > .body"),
        effect = getValidEnumValue(transitionEffect, options.transitionEffect),
        effectSpeed = options.transitionEffectSpeed,
        newStep = stepContents.eq(index),
        currentStep = stepContents.eq(oldIndex);

    switch (effect)
    {
        case transitionEffect.fade:
        case transitionEffect.slide:
            var hide = (effect === transitionEffect.fade) ? "fadeOut" : "slideUp",
                show = (effect === transitionEffect.fade) ? "fadeIn" : "slideDown";

            state.transitionElement = newStep;
            currentStep[hide](effectSpeed, function ()
            {
                var wizard = $(this)._showAria(false).parent().parent(),
                    state = getState(wizard);

                if (state.transitionElement)
                {
                    state.transitionElement[show](effectSpeed, function ()
                    {
                        $(this)._showAria();
                    }).promise().done(doneCallback);
                    state.transitionElement = null;
                }
            });
            break;

        case transitionEffect.slideLeft:
            var outerWidth = currentStep.outerWidth(true),
                posFadeOut = (index > oldIndex) ? -(outerWidth) : outerWidth,
                posFadeIn = (index > oldIndex) ? outerWidth : -(outerWidth);

            $.when(currentStep.animate({ left: posFadeOut }, effectSpeed, 
                    function () { $(this)._showAria(false); }),
                newStep.css("left", posFadeIn + "px")._showAria()
                    .animate({ left: 0 }, effectSpeed)).done(doneCallback);
            break;

        default:
            $.when(currentStep._showAria(false), newStep._showAria())
                .done(doneCallback);
            break;
    }
}

/**
 * Fires when a step click happens.
 *
 * @static
 * @private
 * @event click
 * @param event {Object} An event object
 */
function stepClickHandler(event)
{
    event.preventDefault();

    var anchor = $(this),
        wizard = anchor.parent().parent().parent().parent(),
        options = getOptions(wizard),
        state = getState(wizard),
        oldIndex = state.currentIndex;

    if (anchor.parent().is(":not(.disabled):not(.current)"))
    {
        var href = anchor.attr("href"),
            position = parseInt(href.substring(href.lastIndexOf("-") + 1), 0);

        goToStep(wizard, options, state, position);
    }

    // If nothing has changed
    if (oldIndex === state.currentIndex)
    {
        getStepAnchor(wizard, oldIndex).focus();
        return false;
    }
}

function throwError(message)
{
    if (arguments.length > 1)
    {
        message = message.format(Array.prototype.slice.call(arguments, 1));
    }

    throw new Error(message);
}

/**
 * Checks an argument for null or undefined and throws an error if one check applies.
 *
 * @static
 * @private
 * @method validateArgument
 * @param argumentName {String} The name of the given argument
 * @param argumentValue {Object} The argument itself
 */
function validateArgument(argumentName, argumentValue)
{
    if (argumentValue == null)
    {
        throwError("The argument '{0}' is null or undefined.", argumentName);
    }
}

/**
 * Represents a jQuery wizard plugin.
 *
 * @class steps
 * @constructor
 * @param [method={}] The name of the method as `String` or an JSON object for initialization
 * @param [params=]* {Array} Additional arguments for a method call
 * @chainable
 **/
$.fn.steps = function (method)
{
    if ($.fn.steps[method])
    {
        return $.fn.steps[method].apply(this, Array.prototype.slice.call(arguments, 1));
    }
    else if (typeof method === "object" || !method)
    {
        return initialize.apply(this, arguments);
    }
    else
    {
        $.error("Method " + method + " does not exist on jQuery.steps");
    }
};

/**
 * Adds a new step.
 *
 * @method add
 * @param step {Object} The step object to add
 * @chainable
 **/
$.fn.steps.add = function (step)
{
    var state = getState(this);
    return insertStep(this, getOptions(this), state, state.stepCount, step);
};

/**
 * Removes the control functionality completely and transforms the current state to the initial HTML structure.
 *
 * @method destroy
 * @chainable
 **/
$.fn.steps.destroy = function ()
{
    return destroy(this, getOptions(this));
};

/**
 * Triggers the onFinishing and onFinished event.
 *
 * @method finish
 **/
$.fn.steps.finish = function ()
{
    finishStep(this, getState(this));
};

/**
 * Gets the current step index.
 *
 * @method getCurrentIndex
 * @return {Integer} The actual step index (zero-based)
 * @for steps
 **/
$.fn.steps.getCurrentIndex = function ()
{
    return getState(this).currentIndex;
};

/**
 * Gets the current step object.
 *
 * @method getCurrentStep
 * @return {Object} The actual step object
 **/
$.fn.steps.getCurrentStep = function ()
{
    return getStep(this, getState(this).currentIndex);
};

/**
 * Gets a specific step object by index.
 *
 * @method getStep
 * @param index {Integer} An integer that belongs to the position of a step
 * @return {Object} A specific step object
 **/
$.fn.steps.getStep = function (index)
{
    return getStep(this, index);
};

/**
 * Inserts a new step to a specific position.
 *
 * @method insert
 * @param index {Integer} The position (zero-based) to add
 * @param step {Object} The step object to add
 * @example
 *     $("#wizard").steps().insert(0, {
 *         title: "Title",
 *         content: "", // optional
 *         contentMode: "async", // optional
 *         contentUrl: "/Content/Step/1" // optional
 *     });
 * @chainable
 **/
$.fn.steps.insert = function (index, step)
{
    return insertStep(this, getOptions(this), getState(this), index, step);
};

/**
 * Routes to the next step.
 *
 * @method next
 * @return {Boolean} Indicates whether the action executed
 **/
$.fn.steps.next = function ()
{
    return goToNextStep(this, getOptions(this), getState(this));
};

/**
 * Routes to the previous step.
 *
 * @method previous
 * @return {Boolean} Indicates whether the action executed
 **/
$.fn.steps.previous = function ()
{
    return goToPreviousStep(this, getOptions(this), getState(this));
};

/**
 * Removes a specific step by an given index.
 *
 * @method remove
 * @param index {Integer} The position (zero-based) of the step to remove
 * @return Indecates whether the item is removed.
 **/
$.fn.steps.remove = function (index)
{
    return removeStep(this, getOptions(this), getState(this), index);
};

/**
 * Sets a specific step object by index.
 *
 * @method setStep
 * @param index {Integer} An integer that belongs to the position of a step
 * @param step {Object} The step object to change
 **/
$.fn.steps.setStep = function (index, step)
{
    throw new Error("Not yet implemented!");
};

/**
 * Skips an certain amount of steps.
 *
 * @method skip
 * @param count {Integer} The amount of steps that should be skipped
 * @return {Boolean} Indicates whether the action executed
 **/
$.fn.steps.skip = function (count)
{
    throw new Error("Not yet implemented!");
};

/**
 * An enum represents the different content types of a step and their loading mechanisms.
 *
 * @class contentMode
 * @for steps
 **/
var contentMode = $.fn.steps.contentMode = {
    /**
     * HTML embedded content
     *
     * @readOnly
     * @property html
     * @type Integer
     * @for contentMode
     **/
    html: 0,

    /**
     * IFrame embedded content
     *
     * @readOnly
     * @property iframe
     * @type Integer
     * @for contentMode
     **/
    iframe: 1,

    /**
     * Async embedded content
     *
     * @readOnly
     * @property async
     * @type Integer
     * @for contentMode
     **/
    async: 2
};

/**
 * An enum represents the orientation of the steps navigation.
 *
 * @class stepsOrientation
 * @for steps
 **/
var stepsOrientation = $.fn.steps.stepsOrientation = {
    /**
     * Horizontal orientation
     *
     * @readOnly
     * @property horizontal
     * @type Integer
     * @for stepsOrientation
     **/
    horizontal: 0,

    /**
     * Vertical orientation
     *
     * @readOnly
     * @property vertical
     * @type Integer
     * @for stepsOrientation
     **/
    vertical: 1
};

/**
 * An enum that represents the various transition animations.
 *
 * @class transitionEffect
 * @for steps
 **/
var transitionEffect = $.fn.steps.transitionEffect = {
    /**
     * No transition animation
     *
     * @readOnly
     * @property none
     * @type Integer
     * @for transitionEffect
     **/
    none: 0,

    /**
     * Fade in transition
     *
     * @readOnly
     * @property fade
     * @type Integer
     * @for transitionEffect
     **/
    fade: 1,

    /**
     * Slide up transition
     *
     * @readOnly
     * @property slide
     * @type Integer
     * @for transitionEffect
     **/
    slide: 2,

    /**
     * Slide left transition
     *
     * @readOnly
     * @property slideLeft
     * @type Integer
     * @for transitionEffect
     **/
    slideLeft: 3
};

var stepModel = $.fn.steps.stepModel = {
    title: "",
    content: "",
    contentUrl: "",
    contentMode: contentMode.html,
    contentLoaded: false
};

/**
 * An object that represents the default settings.
 * There are two possibities to override the sub-properties.
 * Either by doing it generally (global) or on initialization.
 *
 * @static
 * @class defaults
 * @for steps
 * @example
 *   // Global approach
 *   $.steps.defaults.headerTag = "h3";
 * @example
 *   // Initialization approach
 *   $("#wizard").steps({ headerTag: "h3" });
 **/
var defaults = $.fn.steps.defaults = {
    /**
     * The header tag is used to find the step button text within the declared wizard area.
     *
     * @property headerTag
     * @type String
     * @default "h1"
     * @for defaults
     **/
    headerTag: "h1",

    /**
     * The body tag is used to find the step content within the declared wizard area.
     *
     * @property bodyTag
     * @type String
     * @default "div"
     * @for defaults
     **/
    bodyTag: "div",

    /**
     * The content container tag which will be used to wrap all step contents.
     *
     * @property contentContainerTag
     * @type String
     * @default "div"
     * @for defaults
     **/
    contentContainerTag: "div",

    /**
     * The action container tag which will be used to wrap the pagination navigation.
     *
     * @property actionContainerTag
     * @type String
     * @default "div"
     * @for defaults
     **/
    actionContainerTag: "div",

    /**
     * The steps container tag which will be used to wrap the steps navigation.
     *
     * @property stepsContainerTag
     * @type String
     * @default "div"
     * @for defaults
     **/
    stepsContainerTag: "div",

    /**
     * The css class which will be added to the outer component wrapper.
     *
     * @property cssClass
     * @type String
     * @default "wizard"
     * @for defaults
     * @example
     *     <div class="wizard">
     *         ...
     *     </div>
     **/
    cssClass: "wizard",

    /**
     * The css class which will be used for floating scenarios.
     *
     * @property clearFixCssClass
     * @type String
     * @default "clearfix"
     * @for defaults
     **/
    clearFixCssClass: "clearfix",

    /**
     * Determines whether the steps are vertically or horizontally oriented.
     *
     * @property stepsOrientation
     * @type stepsOrientation
     * @default horizontal
     * @for defaults
     * @since 1.0.0
     **/
    stepsOrientation: stepsOrientation.horizontal,

    /*
     * Tempplates
     */

    /**
     * The title template which will be used to create a step button.
     *
     * @property titleTemplate
     * @type String
     * @default "<span class=\"number\">#index#.</span> #title#"
     * @for defaults
     **/
    titleTemplate: "<span class=\"number\">#index#.</span> #title#",

    /**
     * The loading template which will be used to create the loading animation.
     *
     * @property loadingTemplate
     * @type String
     * @default "<span class=\"spinner\"></span> #text#"
     * @for defaults
     **/
    loadingTemplate: "<span class=\"spinner\"></span> #text#",

    /*
     * Behaviour
     */

    /**
     * Sets the focus to the first wizard instance in order to enable the key navigation from the begining if `true`. 
     *
     * @property autoFocus
     * @type Boolean
     * @default false
     * @for defaults
     * @since 0.9.4
     **/
    autoFocus: false,

    /**
     * Enables all steps from the begining if `true` (all steps are clickable).
     *
     * @property enableAllSteps
     * @type Boolean
     * @default false
     * @for defaults
     **/
    enableAllSteps: false,

    /**
     * Enables keyboard navigation if `true` (arrow left and arrow right).
     *
     * @property enableKeyNavigation
     * @type Boolean
     * @default true
     * @for defaults
     **/
    enableKeyNavigation: true,

    /**
     * Enables pagination if `true`.
     *
     * @property enablePagination
     * @type Boolean
     * @default true
     * @for defaults
     **/
    enablePagination: true,

    /**
     * Suppresses pagination if a form field is focused.
     *
     * @property suppressPaginationOnFocus
     * @type Boolean
     * @default true
     * @for defaults
     **/
    suppressPaginationOnFocus: true,

    /**
     * Enables cache for async loaded or iframe embedded content.
     *
     * @property enableContentCache
     * @type Boolean
     * @default true
     * @for defaults
     **/
    enableContentCache: true,

    /**
     * Shows the cancel button if enabled.
     *
     * @property enableCancelButton
     * @type Boolean
     * @default false
     * @for defaults
     **/
    enableCancelButton: false,

    /**
     * Shows the finish button if enabled.
     *
     * @property enableFinishButton
     * @type Boolean
     * @default true
     * @for defaults
     **/
    enableFinishButton: true,

    /**
     * Not yet implemented.
     *
     * @property preloadContent
     * @type Boolean
     * @default false
     * @for defaults
     **/
    preloadContent: false,

    /**
     * Shows the finish button always (on each step; right beside the next button) if `true`. 
     * Otherwise the next button will be replaced by the finish button if the last step becomes active.
     *
     * @property showFinishButtonAlways
     * @type Boolean
     * @default false
     * @for defaults
     **/
    showFinishButtonAlways: false,

    /**
     * Prevents jumping to a previous step.
     *
     * @property forceMoveForward
     * @type Boolean
     * @default false
     * @for defaults
     **/
    forceMoveForward: false,

    /**
     * Saves the current state (step position) to a cookie.
     * By coming next time the last active step becomes activated.
     *
     * @property saveState
     * @type Boolean
     * @default false
     * @for defaults
     **/
    saveState: false,

    /**
     * The position to start on (zero-based).
     *
     * @property startIndex
     * @type Integer
     * @default 0
     * @for defaults
     **/
    startIndex: 0,

    /*
     * Animation Effect Configuration
     */

    /**
     * The animation effect which will be used for step transitions.
     *
     * @property transitionEffect
     * @type transitionEffect
     * @default none
     * @for defaults
     **/
    transitionEffect: transitionEffect.none,

    /**
     * Animation speed for step transitions (in milliseconds).
     *
     * @property transitionEffectSpeed
     * @type Integer
     * @default 200
     * @for defaults
     **/
    transitionEffectSpeed: 200,

    /*
     * Events
     */

    /**
     * Fires before the step changes and can be used to prevent step changing by returning `false`. 
     * Very useful for form validation. 
     *
     * @property onStepChanging
     * @type Event
     * @default function (event, currentIndex, newIndex) { return true; }
     * @for defaults
     **/
    onStepChanging: function (event, currentIndex, newIndex) { return true; },

    /**
     * Fires after the step has change. 
     *
     * @property onStepChanged
     * @type Event
     * @default function (event, currentIndex, priorIndex) { }
     * @for defaults
     **/
    onStepChanged: function (event, currentIndex, priorIndex) { },

    /**
     * Fires after cancelation. 
     *
     * @property onCanceled
     * @type Event
     * @default function (event) { }
     * @for defaults
     **/
    onCanceled: function (event) { },

    /**
     * Fires before finishing and can be used to prevent completion by returning `false`. 
     * Very useful for form validation. 
     *
     * @property onFinishing
     * @type Event
     * @default function (event, currentIndex) { return true; }
     * @for defaults
     **/
    onFinishing: function (event, currentIndex) { return true; },

    /**
     * Fires after completion. 
     *
     * @property onFinished
     * @type Event
     * @default function (event, currentIndex) { }
     * @for defaults
     **/
    onFinished: function (event, currentIndex) { },

    /**
     * Fires after async content is loaded. 
     *
     * @property onContentLoaded
     * @type Event
     * @default function (event, index) { }
     * @for defaults
     **/
    onContentLoaded: function (event, currentIndex) { },

    /**
     * Fires when the wizard is initialized. 
     *
     * @property onInit
     * @type Event
     * @default function (event) { }
     * @for defaults
     **/
    onInit: function (event, currentIndex) { },

    /**
     * Contains all labels. 
     *
     * @property labels
     * @type Object
     * @for defaults
     **/
    labels: {
        /**
         * Label for the cancel button.
         *
         * @property cancel
         * @type String
         * @default "Cancel"
         * @for defaults
         **/
        cancel: "Cancel",

        /**
         * This label is important for accessability reasons.
         * Indicates which step is activated.
         *
         * @property current
         * @type String
         * @default "current step:"
         * @for defaults
         **/
        current: "current step:",

        /**
         * This label is important for accessability reasons and describes the kind of navigation.
         *
         * @property pagination
         * @type String
         * @default "Pagination"
         * @for defaults
         * @since 0.9.7
         **/
        pagination: "Pagination",

        /**
         * Label for the finish button.
         *
         * @property finish
         * @type String
         * @default "Finish"
         * @for defaults
         **/
        finish: "Finish",

        /**
         * Label for the next button.
         *
         * @property next
         * @type String
         * @default "Next"
         * @for defaults
         **/
        next: "Next",

        /**
         * Label for the previous button.
         *
         * @property previous
         * @type String
         * @default "Previous"
         * @for defaults
         **/
        previous: "Previous",

        /**
         * Label for the loading animation.
         *
         * @property loading
         * @type String
         * @default "Loading ..."
         * @for defaults
         **/
        loading: "Loading ..."
    }
};
})(jQuery);