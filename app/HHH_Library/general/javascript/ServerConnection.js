class ServerConnection {

    constructor(apiBaseUrl) {

        apiBaseUrl.endsWith("/") ? this.apiBaseUrl = apiBaseUrl : this.apiBaseUrl = apiBaseUrl + "/";

        this.subUrl = ""; //default

        this.setConnectionSetting(this.getConnectionDefaultSetting())
        this.formData = new FormData();
        this.headers = new Array();
        this.appendHeader('Accept','application/json');
        this.appendHeader('X-Requested-With','XMLHttpRequest');
        this.appendHeader('X-CSRF-TOKEN',$('meta[name="csrf-token"]').attr('content'));

    }


    connect() {
        $.ajax(this.getConnectionSetting()).done(function (response) { });
    }

    appendData(key, value) {

        this.formData.append(key, value);
    }

    appendHeader(key, value) {

        this.headers[key] = value;
    }
    /********************* Setter & Getter **********************/
    setSubUrl(subUrl) {
        this.subUrl = subUrl;
    }
    getSubUrl() {
        return this.subUrl;
    }
    getApiFullUrl() {
        return this.apiBaseUrl + this.getSubUrl();
    }

    //post | get | put |...
    setMethod(method) {
        this.method = method;
    }
    getMethod() {
        return this.method;
    }

    setOnSuccess(callbackFunction) {
        this.onSuccess = callbackFunction;
    }
    setOnFailed(callbackFunction) {
        this.onFailed = callbackFunction;
    }

    setConnectionSetting(connectionSetting) {
        this.connectionSetting = connectionSetting;
    }
    getConnectionSetting() {
        /********** Setter Updates ************/
        this.connectionSetting.url = this.getApiFullUrl();
        this.connectionSetting.method = this.getMethod();
        this.connectionSetting.headers = this.headers;
        this.connectionSetting.data = this.formData;
        /********** Setter Updates END ************/

        this.connectionSetting.success = this.onSuccess;
        this.connectionSetting.error = this.onFailed;

        return this.connectionSetting;
    }

    getConnectionDefaultSetting() {

        return {
            url: this.getApiFullUrl(),
            method: "POST",
            timeout: 0,
            processData: false,
            mimeType: "multipart/form-data",
            contentType: false,
            headers: this.headers,
            data: this.formData,
            success: function (response) {
                return response;
            },
            error: function (xhr, status, errorThrown) {
                var msg = "Server Error: \n " + xhr.status + " : " + xhr.statusText;
                msg += "\n\n response: \n" + xhr.responseText;
                alert(msg);
                return xhr;
            },
        };
    }

    /********************* Setter & Getter END **********************/
}


/*
sample ::

    loadData: function(filter) {


                    var apiBaseUrl = "http://trader_engine.cod/api/backoffice/javascript/";
                    var subUrl = "office_categories";

                    var deferred = $.Deferred();
                    var serverConnection = new ServerConnection(apiBaseUrl);
                    serverConnection.setSubUrl((filter.pageIndex>0) ? subUrl+"?page="+filter.pageIndex : subUrl);
                    serverConnection.setMethod("POST");
                    serverConnection.appendData("filter", JSON.stringify(filter));
                    serverConnection.setOnSuccess(function(response){
                        response = JSON.parse(response);

                        var dataResult = {
                            itemsCount: response.meta.total,
                            data: response.data,
                        };
                        deferred.resolve(dataResult);
                    });

                    serverConnection.setOnFailed(function(xhr, status, errorThrown){
                        var msg = "Server Error: \n " + xhr.status + " : " + xhr.statusText;
                        msg += "\n\n response: \n" + xhr.responseText;
                        alert(msg);

                        var dataResult = {
                            itemsCount: 0,
                            data: [],
                        };
                        deferred.resolve(dataResult);

                    });
                    serverConnection.connect();
                    return deferred.promise();
    }


*/
