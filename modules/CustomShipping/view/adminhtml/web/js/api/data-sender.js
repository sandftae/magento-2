define(
    [],
    function () {
        return function () {
            let isSuccess = true;

            let input = document.getElementById('carriers_simpleshipping_import');
            let files = input.files[0];

            let dataToSend = new FormData();

            dataToSend.append('file', files);
            dataToSend.append('form_key', FORM_KEY);

            let xhr = new XMLHttpRequest();

            xhr.open('POST', '/admin/import/import/import/', false);
            xhr.onload = () => {

                if (JSON.parse(xhr.response) === "") {
                    return;
                }
                let response = JSON.parse(xhr.response);
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        if (!response.status) {
                            isSuccess = response.status;
                        }
                    }
                }
            };

            try {
                xhr.send(dataToSend);
            } catch (e) {
                return isSuccess;
            }
            return isSuccess;
        }
    }
);