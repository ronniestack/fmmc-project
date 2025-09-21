@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Calendar</h1>
@stop

@section('content')
    <!-- Calendar section -->
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-sm-12">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Appointment modal details -->
    <form id="appointmentStatusForm" method="POST" action="{{ route('dashboard.update.status') }}">

        @csrf
        <input type="hidden" name="appointment_id" id="modalAppointmentId">
        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Appointment Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p><strong>Client:</strong> <span id="modalAppointmentName">N/A</span></p>
                        <p><strong>Service:</strong> <span id="modalService">N/A</span></p>
                        <p><strong>Email:</strong> <span id="modalEmail">N/A</span></p>
                        <p><strong>Phone:</strong> <span id="modalPhone">N/A</span></p>
                        <p><strong>Doctor:</strong> <span id="modalStaff">N/A</span></p>
                        <p><strong>Date & Time:</strong> <span id="modalStartTime">N/A</span></p>
                        <p><strong>Amount:</strong> <span id="modalAmount">N/A</span></p>
                        <p><strong>Notes:</strong> <span id="modalNotes">N/A</span></p>
                        <p><strong>Current Status:</strong> <span id="modalStatusBadge">N/A</span></p>

                        <div class="form-group">
                            <label><strong>Change Status:</strong></label>
                            <select name="status" class="form-control" id="modalStatusSelect">
                                <option value="Booked">Booked</option>
                                <option value="Rendered">Rendered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update Status</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.css" />
    <style>
        #calendar {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .fc-toolbar h2 {
            font-size: 1.2em;
        }

        .fc-agendaDay-view .fc-time-grid-container {
            height: auto !important;
        }

        .fc-agendaDay-view .fc-event {
            margin: 1px 2px;
            border-radius: 3px;
        }

        .fc-agendaDay-view .fc-event.short-event {
            height: 30px;
            font-size: 0.85em;
            padding: 2px;
        }

        .fc-agendaDay-view .fc-event .fc-content {
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-agendaDay-view .fc-time {
            width: 50px !important;
        }

        .fc-agendaDay-view .fc-time-grid {
            min-height: 600px !important;
        }

        .fc-agendaDay-view .fc-event.fc-short-event {
            height: 35px;
            font-size: 0.85em;
        }

        .fc-agendaDay-view .fc-time {
            width: 70px !important;
            padding: 0 10px;
        }

        .fc-agendaDay-view .fc-axis {
            width: 70px !important;
        }

        .fc-agendaDay-view .fc-content-skeleton {
            padding-bottom: 5px;
        }

        .fc-agendaDay-view .fc-slats tr {
            height: 40px;
        }

        .fc-event {
            opacity: 0.9;
            transition: opacity 0.2s;
        }

        .fc-event:hover {
            opacity: 1;
            z-index: 1000 !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Calendar function
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaDay'
                },
                defaultView: 'month',
                editable: false,
                slotDuration: '00:30:00',
                minTime: '06:00:00',
                maxTime: '22:00:00',
                events: @json($appointments ?? []),
                eventRender: function(event, element) {
                    element.tooltip({
                        title: event.description || 'No description',
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                eventClick: function(calEvent, jsEvent, view) {
                    $('#modalAppointmentId').val(calEvent.id);
                    $('#modalAppointmentName').text(calEvent.name || calEvent.title.split(' - ')[0] || 'N/A');
                    $('#modalService').text(calEvent.service_title || calEvent.title.split(' - ')[1] || 'N/A');
                    $('#modalEmail').text(calEvent.email || 'N/A');
                    $('#modalPhone').text(calEvent.phone || 'N/A');
                    $('#modalStaff').text(calEvent.staff || 'N/A');
                    $('#modalAmount').text(calEvent.amount || 'N/A');
                    $('#modalNotes').text(calEvent.description || calEvent.notes || 'N/A');
                    $('#modalStartTime').text(moment(calEvent.start).format('MMMM D, YYYY h:mm A'));
                    $('#modalEndTime').text(calEvent.end ? moment(calEvent.end).format('MMMM D, YYYY h:mm A') : 'N/A');

                    var status = calEvent.status || 'Booked';
                    $('#modalStatusSelect').val(status);

                    var statusColors = {
                        'Booked': '#3498db',
                        'Rendered': '#2ecc71',
                        'Cancelled': '#ff0000',
                    };

                    var badgeColor = statusColors[status] || '#7f8c8d';
                    $('#modalStatusBadge').html(
                        `<span class="badge px-2 py-1" style="background-color: ${badgeColor}; color: white;">${status}</span>`
                    );

                    $('#appointmentModal').modal('show');
                }
            });
        });

        // Sweet alert toast confirmation
        $("#appointmentStatusForm").on("submit", function(e){
            e.preventDefault();
        
            Swal.fire({
                title: "Are you sure?",
                text: "You want to update the booking status?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    

        // Success toast
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
        // Error toast
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@stop
