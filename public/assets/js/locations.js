/**
 * Module containing core application logic.
 * @param  {jQuery} $        Insulated jQuery object
 * @param  {JSON} settings Insulated `window.snipeit.settings` object.
 * @return {IIFE}          Immediately invoked. Returns self.
 */
(function($, settings) {
    var Components = {};

    Components.table = function() {
        var $el = $('#locations-table');
        var locationName = "";

        var render = function() {
            function getTableColumnDefinitions($table) {
                var $aThs = $table.find("th");
                var aDefs = [];
                $aThs.each(function (iIndex) {
                    var header = $(this);
                    aDefs.push({
                        "aTargets": [ iIndex ],
                        "sCellType": "td",
                        "fnCreatedCell": function ( cell ) {
                            cell.setAttribute("data-title", header.html());
                        }
                    });
                });
                return aDefs;
            }
            var aDefs = getTableColumnDefinitions($el);

            var oTable = $el.dataTable({
                "pageLength": settings.per_page,
                "aoColumnDefs": aDefs,
                "aaSorting": [[ 0, "asc" ]],
                "bSort": true,
                "aoColumns": [
                    null,                   // Name
                    null,                   // Address
                    null,                   // City, State Country
                    { "bSortable": false }  // Actions
                ],
                "bPaginate": true,
                "bAutoWidth": false,
                "sPaginationType": 'full_numbers',
                "bFilter": true,
                "sAjaxSource": "/admin/settings/locations/json",
                "processing": true,
                "serverSide": true,
                "fnServerData": function (sSource, aoData, fnCallback, aParams) {
                    aoData.push(
                        { 'name': 'locationName', 'value': locationName }
                    );
                    $.getJSON(sSource, aoData).done(function (data) {
                        fnCallback(data)
                    });
                }
            });

            $("#locationName").on('keyup', function(){
                locationName = $(this).val();
                oTable.fnDraw();
            });
        };

        return {
            render: render
        };
    };



    /**
     * Application start point
     * Component definition stays out of load event, execution only happens.
     */
    $(function() {
        new Components.table().render();
    });
}(jQuery, window.snipeit.settings));