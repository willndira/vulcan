/**
 * Theme: Minton Admin Template
 * Author: Coderthemes
 * Component: Full-Calendar
 *
 */




!function ($) {
    "use strict";

    var CalendarApp = function () {
        this.$body = $("body")
        this.$modal = $('#event-modal'),
            this.$event = ('#external-events div.external-event'),
            this.$calendar = $('#calendar'),
            this.$saveCategoryBtn = $('.save-category'),
            this.$categoryForm = $('#add-category form'),
            this.$extEvents = $('#external-events'),
            this.$calendarObj = null
    };


    /* on drop */
    CalendarApp.prototype.onDrop = function (eventObj, date) {
        var $this = this;
        //flag visit as timed out
        if (date < new Date($.now())) {
            $.Notification.autoHideNotify('error', 'top right', 'Timed Out', 'Sorry the visit plan failed. You need to place for future.');
            return;
        }
        var originalEventObject = eventObj.data('eventObject');
        var $categoryClass = eventObj.attr('data-class');
        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);
        // assign it the date that was reported
        copiedEventObject.start = date;
        if ($categoryClass)
            copiedEventObject['className'] = [$categoryClass];
        var request = $.ajax({
            method: 'POST',
            data: {date: copiedEventObject.start.format(), outlet: eventObj.attr('id')},
            url: siteurl + "my_plans/create"
        });
        request.done(function (msg) {
            var isSuccess = true;
            try {
                JSON.parse(msg);
            } catch (e) {
                isSuccess = false;
            }

            if (isSuccess) {
                copiedEventObject['id'] = JSON.parse(msg).id;
                $this.$calendar.fullCalendar('renderEvent', copiedEventObject, true);
                if ($('#drop-remove').is(':checked'))
                    eventObj.remove();
                $.Notification.autoHideNotify('success', 'top right', 'Plan Successful', copiedEventObject.title + ' has been added to your plans successfully');
            } else {
                $.Notification.autoHideNotify('error', 'top right', 'Plan Failed', msg);
            }
        });

    },
        /* on click on event */
        CalendarApp.prototype.onEventClick = function (calEvent, jsEvent, view) {
            window.location = siteurl.concat('visit/' + calEvent.id);
        },
        /* on select */
        CalendarApp.prototype.onSelect = function (start, end, allDay) {
            if (start < new Date($.now())) {
                $.Notification.autoHideNotify('error', 'top right', 'Too late', 'Sorry the visit plan failed. You need to place for future.');
                return;
            }
            var $this = this;
            $this.$modal.modal({
                backdrop: 'static'
            });
            var form = $("<form></form>");
            form.append("<div class='row'></div>");
            form.find(".row")
                .append("<div class='col-md-12'><div class='form-group'><label class='control-label'>Select Outlet</label>" +
                    "<select class='form-control' name='outlet'></select></div></div>");
            for (i in outlets) {
                form.find("select[name='outlet']")
                    .append("<option value='" + outlets[i].outlet_code + "'>" + outlets[i].outlet_name + "</option>")
            }
            form.append("</div></div>");
            $this.$modal.find('.delete-event').hide().end().find('.save-event').show().end().find('.modal-body').empty().prepend(form).end().find('.save-event').unbind('click').click(function () {
                form.submit();
            });
            $this.$modal.find('form').on('submit', function () {
                var outlet = form.find("select[name='outlet'] option:checked").val();
                var date = form.find("input[name='beginning']").val();
                var title = form.find("select[name='outlet'] option:checked").text();
                if (outlet !== null && outlet.length != 0) {
                    var request = $.ajax({
                        method: 'POST',
                        data: {date: start.format(), outlet: outlet},
                        url: siteurl + "my_plans/create"
                    });
                    request.done(function (msg) {
                        var isSuccess = true;
                        try {
                            JSON.parse(msg);
                        } catch (e) {
                            isSuccess = false;
                        }

                        if (isSuccess) {
                            $.Notification.autoHideNotify('success', 'top right', 'Plan Successful', title + ' has been added to your plans successfully');
                            $this.$calendarObj.fullCalendar('renderEvent', {
                                title: title,
                                id: JSON.parse(msg).id,
                                start: start,
                                allDay: false,
                                className: "bg-primary"
                            }, true);
                        } else {
                            $.Notification.autoHideNotify('error', 'top right', 'Plan Failed', msg);
                        }
                    });

                    $this.$modal.modal('hide');
                }
                else {
                    alert('You have to select an outlet');
                }
                return false;

            });
            $this.$calendarObj.fullCalendar('unselect');
        },
        CalendarApp.prototype.enableDrag = function () {
            //init events
            $(this.$event).each(function () {
                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                };
                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject);
                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                });
            });
        }
    /* Initializing */
    CalendarApp.prototype.init = function () {
        this.enableDrag();
        /*  Initialize the calendar  */
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var form = '';
        var today = new Date($.now());

        var $this = this;
        $this.$calendarObj = $this.$calendar.fullCalendar({
            slotDuration: '00:20:00', /* If we want to split day time each 20 minutes */
            minTime: '00:00:00',
            maxTime: '23:59:59',
            defaultView: 'agendaWeek',
            handleWindowResize: true,
            height: $(window).height(),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: defaultEvents,
            editable: false,
            defaultTimedEventDuration: '00:20',
            droppable: true, // this allows things to be dropped onto the calendar !!!
            eventLimit: true, // allow "more" link when too many events
            selectable: true,
            drop: function (date) {
                $this.onDrop($(this), date);
            },
            select: function (start, end, allDay) {
                $this.onSelect(start, end, allDay);
            },
            eventClick: function (calEvent, jsEvent, view) {
                $this.onEventClick(calEvent, jsEvent, view);
            },

        });

        //on new event
        this.$saveCategoryBtn.on('click', function () {
            var categoryName = $this.$categoryForm.find("input[name='category-name']").val();
            var categoryColor = $this.$categoryForm.find("select[name='category-color']").val();
            if (categoryName !== null && categoryName.length != 0) {
                $this.$extEvents.append('<div class="external-event bg-' + categoryColor + '" data-class="bg-' + categoryColor + '" style="position: relative;"><i class="fa fa-move"></i>' + categoryName + '</div>')
                $this.enableDrag();
            }

        });
    },

        //init CalendarApp
        $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp

}(window.jQuery),

//initializing CalendarApp
    function ($) {
        "use strict";
        $.CalendarApp.init()
    }(window.jQuery);
