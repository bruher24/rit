$(document).ready(function () {
    let table = $('#actives-table');
    let actives = [];
    getActivesRequest().then((response) => {
        actives = response;
        actives.forEach((active) => {
            let detailsString = '';
            let avoidKeys = [
                'id',
                'name',
                'type',
                'totalCost',
                'startBalanceCost',
                'residualBalanceCost',
                'finalBalanceCost',
            ];
            for (let key of Object.keys(active)) {
                if (avoidKeys.indexOf(key) < 0) {
                    let detail = formatDetail(key, active[key]);
                    detailsString += detail + ', ';
                }
            }
            detailsString = detailsString.slice(0, -2);
            detailsString = detailsString[0].toUpperCase() + detailsString.slice(1);
            let cost = formatCost(active);
            let tr = "<tr>" +
                `<td>${active.id}</td>` +
                `<td>${active.name}</td>` +
                `<td>${active.type === 'money' ? 'Денежный' : 'Иной'}</td>` +
                `<td>${detailsString}</td>` +
                `<td>${cost}</td>` +
                `<td>
                    <button class="btn btn-sm btn-warning formBtn" id='updateBtn' data-active='${active.id}''>
                        Изменить
                    </button>
                    <form action="/actives/${active.id}/delete" method="post" style="display:inline;">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот актив?')">Удалить</button>
                    </form>
                </td>` +
                "</tr>";
            table.append(tr);
        });

        const modal = new bootstrap.Modal('#modalForm');
        const moneySelector = $('.hiddenMoneyInput');
        const nonMoneySelector = $('.hiddenNonMoneyInput');

        $('.formBtn').click(function () {
            moneySelector.hide();
            nonMoneySelector.hide();
            $('#activeForm')[0].reset();
        });

        $('#createBtn').click(function () {
            $('#modalTitle').html('Создание актива');
            $('#activeId').val('');
        });

        $('#updateBtn').click(function () {
            let activeId = $(this).data('active');
            let active = actives.find(a => a.id == activeId);
            $('#modalTitle').html(`Изменение актива №${activeId}`);
            $('#activeId').val(activeId);
            $('#floatingType').val(active.type).change();
            modal.show();
        });

        $('#floatingType').on('change', function () {
            moneySelector.hide();
            nonMoneySelector.hide();

            let activeId = $('#activeId').val();
            let active = actives.find(a => a.id == activeId);
            if (active) {
                if (active.type === this.value) {
                    fillForm(active);
                } else {
                    let temp = this.value;
                    $('#activeForm')[0].reset();
                    this.value = temp;
                }
            }
            if (this.value === 'money') {
                moneySelector.show();
            } else {
                nonMoneySelector.show();
            }
        });

        $('#submitBtn').click(function () {
            var data = $('#activeForm').serializeArray().reduce(function (obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});

            let formattedData = formatData(data);

            let method = 'create';
            let activeId = $('#activeId').val();
            if (activeId) {
                formattedData.id = activeId;
                method = 'update';
            }

            sendData(formattedData, method).then((response) => {
                if (response.errors) {
                    displayErrors(response.errors);
                    return;
                }
                window.location.replace('/');
            });
        });
    });
});

async function getActivesRequest() {
    return await $.ajax({
        url: 'actives',
        method: 'get',
        async: true
    });
}

function fillForm(active) {
    $('#floatingName').val(active.name);
    $('#floatingType').val(active.type);

    let selector = active.type === 'money' ? '.hiddenMoneyInput' : 'hiddenNonMoneyInput';
    let inputs = $(selector).children('.form-control');

    inputs.each(function () {
        let property = this.name;
        $(this).val(active[property])
    })
}

function formatDetail(key, value) {
    switch (key) {
        case 'bankName':
            return `банк: ${value}`;
        case 'accountNumber':
            return `счет: ${value}`;
        case 'currency':
            return `валюта: ${value}`;
        case 'inventoryNumber':
            return `инв. номер: ${value}`;
        case 'measureUnits':
            return `ед. изм.: ${value}`;
        case 'productionDate':
            return `дата произв.: ${value}`;
    }
}

function formatData(data) {
    let result = {};
    result.name = data.name;
    result.type = data.type;

    let selector = data.type === 'money' ? '.hiddenMoneyInput' : '.hiddenNonMoneyInput';
    let inputs = $(selector).children('.form-control');

    inputs.each(function () {
        let property = this.name;
        result[property] = data[property].trim();
    })

    if (result.bankName) {
        delete result.currency;
    }

    return result;
}

async function sendData(data, type) {
    let url = 'actives/create';
    if (type === 'update') {
        url = `actives/${data.id}/update`;
    }
    return await $.ajax({
        url: url,
        method: 'post',
        async: true,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        data: data
    });
}

function displayErrors(errors) {
    let header = $('#header');
    for (const field of Object.keys(errors)) {
        errors[field].forEach((error) => {
            let alert = "<div class='alert alert-danger alert-dismissible fade show errorAlert' role='alert' style='z-index: 999999;'>" +
                `<p>${error}</p>` +
                "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>" +
                "</div>";
            header.append(alert);
        });
    }
}

function formatCost(active) {
    if (active.totalCost) {
        return active.totalCost;
    }
    return `Нач.: ${active.startBalanceCost}` + "<br>"
        + `Ост.: ${active.residualBalanceCost}` + "<br>"
        + `Оцен.: ${active.finalBalanceCost}`;
}
