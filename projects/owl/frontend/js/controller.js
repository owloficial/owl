/**
 * Created with JetBrains PhpStorm.
 * User: francismararaujo
 * Date: 30/01/14
 * Time: 16:31
 * To change this template use File | Settings | File Templates.
 */
function Controller($scope) {

    $scope.controlActions = function(widget, checkedRows) {

        var keys = Object.keys(checkedRows);
        var showAsAction = widget.container.getActions();

        for (var i = 0; i < keys.length; i++) {

            var val = checkedRows[keys[i]];

            if(val == true){
                showAsAction[0]['showAsAction'] = 'always';
                showAsAction[1]['showAsAction'] = 'always';
            }else{
                showAsAction[0]['showAsAction'] = false;
                showAsAction[1]['showAsAction'] = false;
            }

        }

    };

}