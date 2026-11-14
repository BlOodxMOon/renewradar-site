(function () {
  var form = document.getElementById('calc');
  if (!form) return;

  var rowsEl = document.getElementById('rows');
  var currencyEl = document.getElementById('currency');
  var PER_MONTH = { weekly: 52 / 12, monthly: 1, quarterly: 1 / 3, yearly: 1 / 12 };

  var REMOVE = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';

  function addRow(name, price, cycle) {
    var row = document.createElement('div');
    row.className = 'calc-row';
    row.innerHTML =
      '<input class="field name" type="text" maxlength="40" placeholder="Subscription name" aria-label="Subscription name">' +
      '<input class="field price" type="number" inputmode="decimal" min="0" step="0.01" placeholder="0.00" aria-label="Price">' +
      '<select class="cycle" aria-label="Billing cycle">' +
      '<option value="weekly">Weekly</option><option value="monthly">Monthly</option>' +
      '<option value="quarterly">Quarterly</option><option value="yearly">Yearly</option></select>' +
      '<button type="button" class="icon-btn" aria-label="Remove">' +
      REMOVE +
      '</button>';
    row.querySelector('.name').value = name || '';
    row.querySelector('.price').value = price != null ? price : '';
    row.querySelector('.cycle').value = cycle || 'monthly';
    row.querySelector('.icon-btn').addEventListener('click', function () {
      row.remove();
      update();
    });
    rowsEl.appendChild(row);
    return row;
  }

  function money(n) {
    var code = currencyEl.value;
    try {
      return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: code,
        maximumFractionDigits: code === 'JPY' ? 0 : 2,
      }).format(n);
    } catch (e) {
      return code + ' ' + n.toFixed(2);
    }
  }

  function update() {
    var items = [];
    [].forEach.call(rowsEl.querySelectorAll('.calc-row'), function (row) {
      var price = parseFloat(row.querySelector('.price').value);
      if (!(price > 0)) return;
      var name = row.querySelector('.name').value.trim() || 'Untitled';
      items.push({ name: name, monthly: price * PER_MONTH[row.querySelector('.cycle').value] });
    });

    var total = items.reduce(function (s, i) {
      return s + i.monthly;
    }, 0);

    document.getElementById('out-month').textContent = money(total);
    document.getElementById('out-year').textContent = money(total * 12);
    document.getElementById('out-day').textContent = money((total * 12) / 365);
    document.getElementById('out-five').textContent = money(total * 60);
    document.getElementById('out-count').textContent =
      items.length === 1 ? '1 subscription' : items.length + ' subscriptions';

    var bars = document.getElementById('bars');
    bars.innerHTML = '';
    items
      .sort(function (a, b) {
        return b.monthly - a.monthly;
      })
      .slice(0, 6)
      .forEach(function (i) {
        var share = total > 0 ? Math.round((i.monthly / total) * 100) : 0;
        var li = document.createElement('li');
        var row = document.createElement('div');
        row.className = 'row';
        var label = document.createElement('span');
        label.textContent = i.name;
        var value = document.createElement('span');
        value.className = 'num';
        value.textContent = money(i.monthly * 12) + ' / yr';
        row.appendChild(label);
        row.appendChild(value);
        var track = document.createElement('div');
        track.className = 'track';
        var fill = document.createElement('div');
        fill.className = 'fill';
        fill.style.width = share + '%';
        track.appendChild(fill);
        li.appendChild(row);
        li.appendChild(track);
        bars.appendChild(li);
      });
  }

  form.addEventListener('input', update);
  form.addEventListener('change', update);
  form.addEventListener('submit', function (e) {
    e.preventDefault();
  });

  document.getElementById('add-row').addEventListener('click', function () {
    addRow().querySelector('.name').focus();
  });

  [].forEach.call(document.querySelectorAll('[data-add]'), function (chip) {
    chip.addEventListener('click', function () {
      addRow(chip.getAttribute('data-add'), '', chip.getAttribute('data-cycle') || 'monthly')
        .querySelector('.price')
        .focus();
    });
  });

  addRow('Video streaming', 15.99, 'monthly');
  addRow('Music', 11.99, 'monthly');
  addRow('Cloud storage', 2.99, 'monthly');
  addRow('Shopping membership', 139, 'yearly');
  update();
})();
