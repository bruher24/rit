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
                'totalCost'
            ];
            for (let key of Object.keys(active)) {
                if (avoidKeys.indexOf(key) < 0) {
                    let detail = formatDetail(key, active[key]);
                    detailsString += detail + ', ';
                }
            }
            detailsString = detailsString.slice(0, -2);
            detailsString = detailsString[0].toUpperCase() + detailsString.slice(1);
            let tr = "<tr>" +
                `<td>${active.id}</td>` +
                `<td>${active.name}</td>` +
                `<td>${active.type === 'money' ? 'Денежный' : 'Иной'}</td>` +
                `<td>${detailsString}</td>` +
                `<td>${active.totalCost}</td>` +
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
                    $('#activeForm')[0].reset();
                }
            }
            if (this.value === 'money') {
                moneySelector.show();
            } else {
                nonMoneySelector.show();
            }
        });

        $('#submitBtn').click(function () {
            let data = $('#activeForm').serializeArray();
            console.log(data);
            if(data.)
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
