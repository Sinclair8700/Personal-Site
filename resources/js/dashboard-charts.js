// Interactive readout tables for the dashboard bar charts. Hovering (or
// focusing) a bar previews that day's detail; clicking pins it in place until
// the same bar is clicked again. User-supplied text (emails, messages) is only
// ever set via textContent, so it can't inject markup.

function makeEl(tag, className, text) {
    const node = document.createElement(tag);
    if (className) {
        node.className = className;
    }
    if (text !== undefined && text !== null) {
        node.textContent = text;
    }
    return node;
}

function stickyHeader(text) {
    return makeEl('th', 'font-medium py-1 pr-3 sticky top-0 bg-black', text);
}

function renderVisitorDay(bar, total) {
    const count = Number(bar.dataset.count || 0);
    const share = total ? Math.round((count / total) * 100) : 0;

    const wrap = document.createElement('div');
    wrap.append(makeEl('div', 'text-white/50 text-xs mb-2', bar.dataset.label));

    const table = makeEl('table', 'w-full text-sm');
    const tbody = makeEl('tbody');

    [
        ['New visitors', String(count)],
        ['Share of 30 days', share + '%'],
    ].forEach(([key, value]) => {
        const row = makeEl('tr', 'border-t border-white/10 first:border-t-0');
        row.append(makeEl('td', 'py-1 text-white/60', key));
        row.append(makeEl('td', 'py-1 text-white text-right font-medium', value));
        tbody.append(row);
    });

    table.append(tbody);
    wrap.append(table);
    return wrap;
}

function renderMessagesDay(bar) {
    let items = [];
    try {
        items = JSON.parse(bar.dataset.items || '[]');
    } catch (error) {
        items = [];
    }

    const wrap = document.createElement('div');
    const noun = items.length === 1 ? 'message' : 'messages';
    wrap.append(makeEl('div', 'text-white/50 text-xs mb-2', `${bar.dataset.label} · ${items.length} ${noun}`));

    if (!items.length) {
        wrap.append(makeEl('p', 'text-white/50 text-sm', 'No messages on this day.'));
        return wrap;
    }

    const scroll = makeEl('div', 'max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-white/20');
    const table = makeEl('table', 'w-full text-sm border-collapse');

    const thead = makeEl('thead');
    const headRow = makeEl('tr', 'text-white/50 text-xs text-left');
    ['Time', 'Email', 'Message'].forEach(label => headRow.append(stickyHeader(label)));
    thead.append(headRow);
    table.append(thead);

    const tbody = makeEl('tbody');
    items.forEach(item => {
        const row = makeEl('tr', 'border-t border-white/10 align-top');
        row.append(makeEl('td', 'py-2 pr-3 text-white/60 whitespace-nowrap', item.time || ''));

        const emailCell = makeEl('td', 'py-2 pr-3 text-white whitespace-nowrap');
        const link = makeEl('a', 'underline decoration-white/20 hover:text-purple', item.email || '');
        link.href = 'mailto:' + (item.email || '');
        emailCell.append(link);
        row.append(emailCell);

        row.append(makeEl('td', 'py-2 text-white/80', item.message || ''));
        tbody.append(row);
    });
    table.append(tbody);
    scroll.append(table);
    wrap.append(scroll);
    return wrap;
}

function initBarChart(chart) {
    const bars = Array.from(chart.querySelectorAll('.chart-bar'));
    const readout = chart.querySelector('[data-readout]');
    if (!bars.length || !readout) {
        return;
    }

    const isMessages = chart.dataset.chartType === 'messages';
    const defaultHtml = readout.innerHTML;
    const total = bars.reduce((sum, bar) => sum + Number(bar.dataset.count || 0), 0);

    let pinned = null;

    const draw = (bar) => {
        const node = isMessages ? renderMessagesDay(bar) : renderVisitorDay(bar, total);
        readout.replaceChildren(node);
    };

    const restore = () => {
        if (pinned) {
            draw(pinned);
        } else {
            readout.innerHTML = defaultHtml;
        }
    };

    bars.forEach(bar => {
        bar.addEventListener('mouseenter', () => draw(bar));
        bar.addEventListener('focus', () => draw(bar));
        bar.addEventListener('mouseleave', restore);
        bar.addEventListener('blur', restore);

        bar.addEventListener('click', () => {
            if (pinned === bar) {
                pinned = null;
                bar.classList.remove('is-pinned');
                bar.setAttribute('aria-pressed', 'false');
                readout.innerHTML = defaultHtml;
                return;
            }

            if (pinned) {
                pinned.classList.remove('is-pinned');
                pinned.setAttribute('aria-pressed', 'false');
            }

            pinned = bar;
            bar.classList.add('is-pinned');
            bar.setAttribute('aria-pressed', 'true');
            draw(bar);
        });
    });
}

document.querySelectorAll('[data-bar-chart]').forEach(initBarChart);
