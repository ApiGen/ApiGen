// open details
document.querySelectorAll('.expandable').forEach(el => {
	el.addEventListener('click', e => {
		e.currentTarget.classList.toggle('collapsed')
	})
})


// table sort
document.querySelectorAll('.sortable').forEach(el => {
	const table = el.closest('table')
	const items = Array.from(table.querySelectorAll('[data-order]'))
	let orderBy = 'order'

	items.forEach((item, i) => {
		item.dataset.orderNatural = i.toString(10).padStart(3, '0')
	})

	el.addEventListener('click', () => {
		items.sort((a, b) => a.dataset[orderBy].localeCompare(b.dataset[orderBy]))
		table.append(...items)
		orderBy = orderBy === 'order' ? 'orderNatural' : 'order'
	})
})


// line selection
let ranges = []
let last = null
const match = window.location.hash.slice(1).match(/^\d+(?:-\d+)?(?:,\d+(?:-\d+)?)*$/)

const handleLinesSelectionChange = () => {
	history.replaceState({}, '', '#' + ranges.map(([a, b]) => a === b ? a : `${a}-${b}`).join(','));
	document.querySelectorAll('.source-line.selected').forEach(el => el.classList.remove('selected'))

	for (let [a, b] of ranges) {
		for (let i = a; i <= b; i++) {
			document.getElementById(`${i}`).classList.add('selected')
		}
	}
}

if (match) {
	ranges = match[0].split(',').map(range => range.split('-').map(n => Number.parseInt(n)))
	ranges = ranges.map(([a, b]) => b === undefined ? [a, a] : [a, b])
	ranges = ranges.filter(([a, b]) => a <= b)
	handleLinesSelectionChange()

	const first = Math.max(1, Math.min(...ranges.flat()) - 3)
	document.getElementById(`${first}`).scrollIntoView()
}

document.querySelectorAll('.source-lineNum').forEach(a => {
	a.addEventListener('click', e => {
		e.preventDefault()
		const line = e.currentTarget.closest('tr')
		const n = Number.parseInt(line.id)
		const selected = line.classList.contains('selected') && e.ctrlKey
		const extending = e.shiftKey && ranges.length > 0

		if (!e.ctrlKey) {
			ranges = extending ? [ranges[ranges.length - 1]] : []
		}

		if (extending) {
			ranges[ranges.length - 1] = [Math.min(n, last), Math.max(n, last)]

		} else if (selected) {
			ranges = ranges.flatMap(([a, b]) => (a <= n && n <= b) ? [[a, n - 1], [n + 1, b]] : [[a, b]])
			ranges = ranges.filter(([a, b]) => a <= b)

		} else {
			ranges[ranges.length] = [n, n]
			last = n
		}

		handleLinesSelectionChange()
	})
})
