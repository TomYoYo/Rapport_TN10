$(document).ready(function () {
    $.ajax({
        dataType: "json",
        type: "GET",
        url: '../ajax/list_nodes/',
        success: function (response) {
            var zTreeObj;
            // zTree configuration information, refer to API documentation (setting details)
            var setting = {
                view: {dblClickExpand: false},
                data: {simpleData: {enable: true}},
                callback: {onClick: onClick}
            };
            // zTree data attributes, refer to the API documentation (treeNode data details)
            zTreeObj = $.fn.zTree.init($("#treeDAN"), setting, response);
        }
    });
});

function onClick(e, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("treeDAN");
    zTree.expandNode(treeNode);
}