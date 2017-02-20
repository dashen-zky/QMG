function trim(str){
    return str.replace(/(^[\s\,]*)|([\s\,]*$)/g, "");
}

function validateSeriesNumber(bank_series_number) {
    var reg = /^\w+$/;
    return reg.test(bank_series_number);
}

function in_array(search,array){
    for(var i in array){
        if(array[i]==search){
            return true;
        }
    }
    return false;
}

function pagination() {
    var self = arguments[0];
    var afterHandler = '';
    if(arguments.length == 2) {
        afterHandler = arguments[1];
    }
    var url = self.attr('url');
    $.get(
        url,
        function(data, status) {
            if(status !== 'success') {
                return ;
            }
            var container = self.parents('.list');
            container.html(data);
            if(afterHandler !== '') {
                afterHandler();
            }

            container.find('.pagination').on('click', 'li', function() {
                pagination($(this), afterHandler);
            });
        });
}