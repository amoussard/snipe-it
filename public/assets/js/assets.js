/**
 * Module containing core application logic.
 * @param  {jQuery} $        Insulated jQuery object
 * @param  {JSON} settings Insulated `window.snipeit.settings` object.
 * @return {IIFE}          Immediately invoked. Returns self.
 */
(function($, settings) {
    var Components = {};

    Components.table = function() {
        var $el = $('#assets-table');
        var assetMac = "";
        var assetName = "";
        var assetModel = "";
        var assetStatus = "";
        var assetLocation = "";
        var assetOrderNumber = "";
        var assetId = "";
        var assetNumedia = 0;

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
                    null,                   // Mac
                    null,                   // Name
                    null,                   // Model
                    null,                   // Status
                    null,                   // Location
                    { "bSortable": false }, // In/out
                    { "bSortable": false }  // Actions
                ],
                "bPaginate": true,
                "bAutoWidth": false,
                "sPaginationType": 'full_numbers',
                "bFilter": false,
                "sAjaxSource": "/hardware/json",
                "processing": true,
                "serverSide": true,
                "fnServerData": function (sSource, aoData, fnCallback, aParams) {
                    aoData.push(
                        { 'name': 'assetMac', 'value': assetMac },
                        { 'name': 'assetName', 'value': assetName },
                        { 'name': 'assetLocation', 'value': assetLocation },
                        { 'name': 'assetModel', 'value': assetModel },
                        { 'name': 'assetStatus', 'value': assetStatus },
                        { 'name': 'assetNumedia', 'value': assetNumedia },
                        { 'name': 'assetOrderNumber', 'value': assetOrderNumber },
                        { 'name': 'assetId', 'value': assetId }
                    );
                    $.getJSON(sSource, aoData).done(function (data) {
                        fnCallback(data);
                    });
                }
            });

            $("#assetMac").on('keyup', function(){
                assetMac = $(this).val();
                oTable.fnDraw();
            });
            $("#assetName").on('keyup', function(){
                assetName = $(this).val();
                oTable.fnDraw();
            });
            $("#assetLocation").on('keyup', function(){
                assetLocation = $(this).val();
                oTable.fnDraw();
            });
            $("#assetModel").on('change', function(){
                assetModel = $(this).val();
                oTable.fnDraw();
            });
            $("#assetStatus").on('change', function(){
                assetStatus = $(this).val();
                oTable.fnDraw();
            });
            $("#assetOrderNumber").on('keyup', function(){
                assetOrderNumber = $(this).val();
                oTable.fnDraw();
            });
            $("#assetId").on('keyup', function(){
                assetId = $(this).val();
                oTable.fnDraw();
            });
            $("#assetNumedia").on('click', function(){
                if ($(this).is(':checked')) {
                    assetNumedia = 1;
                } else {
                    assetNumedia = 0;
                }
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