BX.namespace('Aclips.Report')

BX.Aclips.Report = {
    reportId: null,
    signedParameters: null,
    reportContent: null,
    reportName: null,
    reportFormat: null,
    applyFilter: function () {
        try {
            BX.Main.filterManager.getById(this.reportId).applyFilter()
        } catch (e) {
            this.preview()
        }

    },
    preview: function () {
        let self = this

        BX.addClass(BX('workarea-content'), 'loader')
        BX.removeClass(BX('workarea-content'), 'no-report')

        let errorsNode = BX('errors')
        BX.cleanNode(errorsNode)

        let reportWrapperNode = BX('report-wrapper')
        BX.cleanNode(reportWrapperNode)

        BX.ajax.runComponentAction('aclips:report.detail', 'applyFilter',
            {
                mode: 'class',
                signedParameters: this.signedParameters
            }).then((response) => {

            self.reportContent = response.data.result
            self.reportName = response.data.file_name
            self.reportFormat = response.data.file_format

            reportWrapperNode.insertAdjacentHTML('beforeend', "<span onclick='BX.Aclips.Report.download()' class='diagramm_save ui-btn ui-btn-active'>Скачать файл</span>")

            let html = new DOMParser().parseFromString(response.data.html, "text/html")

            let tables = html.getElementsByTagName('table')
            let table = tables[0]
            reportWrapperNode.append(table)

            let styles = html.getElementsByTagName('style')
            let style = styles[0]
            reportWrapperNode.append(style)

            BX.removeClass(BX('workarea-content'), 'loader')

        }, (reject) => {
            BX.removeClass(BX('workarea-content'), 'loader')

            let error = '';

            reject.errors.forEach(function (item, i, reject) {
                error = BX.create({
                    tag: 'div',
                    props: {
                        className: 'ui-alert ui-alert-danger'
                    },
                    html: '<span class="ui-alert-message">' + item.message + '</span>'
                });

                BX.prepend(error, errorsNode);

                BX.scrollToNode(errorsNode);
            });
        })
    },
    download: function () {
        let fileName = this.reportName + "." + this.reportFormat

        let downloadLink = document.createElement('a')

        downloadLink.setAttribute('href', this.reportContent)
        downloadLink.setAttribute('download', fileName)
        downloadLink.click()
    }
}

BX.ready(function () {
    BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command, params) {
        if (BX.Aclips.Report.reportId == command && params.action == 'apply') {
            BX.Aclips.Report.preview()
        }
    }));
});