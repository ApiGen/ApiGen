window.addEventListener('DOMContentLoaded', () => {
	// menu
	document.querySelectorAll('#groups span').forEach(el => {
		el.addEventListener('click', e => {
			e.preventDefault()
			e.currentTarget.closest('li').classList.toggle('collapsed')
		})
	})

	// open details
	document.querySelectorAll('.summary tr').forEach(el => {
		el.addEventListener('click', e => {
			e.currentTarget.classList.toggle('collapsed')
		})
	})

// 	// line selection
// 	let ranges = []
// 	const match = window.location.hash.substr(1).match(/^\d+(?:-\d+)?(?:,\d+(?:-\d+)?)*$/)
//
// 	const redrawLinesSelection = () => {
// 		document.querySelectorAll('div.l.selected').forEach(el => el.classList.remove('selected'))
//
// 		for (let [a, b] of ranges) {
// 			const x = Math.min(a, b ?? a)
// 			const y = Math.max(a, b ?? a)
//
// 			for (let i = x; i <= y; i++) {
// 				document.getElementById(`${i}`).classList.add('selected')
// 			}
// 		}
// 	}
//
// 	if (match) {
// 		ranges = match[0].split(',').map(range => range.split('-').map(n => Number.parseInt(n)))
// 		redrawLinesSelection()
//
// 		const first = Math.max(1, Math.min(...ranges.flat()) - 3)
// 		document.getElementById(`${first}`).scrollIntoView()
// 	}
//
//
// 	document.querySelectorAll('a.l').forEach(a => {
// 		a.addEventListener('click', e => {
// 			e.preventDefault()
// 			const line = e.currentTarget.closest('div')
// 			const lineNumber = Number.parseInt(line.id)
// 			const selected = line.classList.contains('selected')
// 			const extending = e.shiftKey && ranges.length > 0
//
// 			if (!e.ctrlKey) {
// 				ranges = extending ? [ranges[ranges.length - 1]] : []
// 			}
//
// 			if (extending) {
// 				ranges[ranges.length - 1][1] = lineNumber
//
// 			} else if (selected && e.ctrlKey) {
// 				ranges = ranges.flatMap(range => {
// 					const x = Math.min(range[0], range[1] ?? range[0])
// 					const y = Math.max(range[0], range[1] ?? range[0])
//
// 					if (x === lineNumber && y === lineNumber) {
// 						return []
// 					} else if (x === lineNumber && lineNumber < y) {
// 						return [[lineNumber + 1, y]]
// 					} else if (y === lineNumber && x < lineNumber) {
// 						return [[x, lineNumber - 1]]
// 					} else if (x < lineNumber && lineNumber < y) {
// 						return [[x, lineNumber - 1], [lineNumber + 1, y]]
// 					} else {
// 						return [range]
// 					}
// 				})
//
// 			} else {
// 				ranges.push([lineNumber])
// 			}
//
// 			ranges = ranges.map(range => range[0] === range[1] ? [range[0]] : range)
// 			history.replaceState({}, '', '#' + ranges.map(range => range.join('-')).join(','));
// 			redrawLinesSelection()
// 		})
// 	})

	// line selection
	let ranges = []
	let last = null
	const match = window.location.hash.substr(1).match(/^\d+(?:-\d+)?(?:,\d+(?:-\d+)?)*$/)

	const handleLinesSelectionChange = () => {
		history.replaceState({}, '', '#' + ranges.map(([a, b]) => a === b ? a : `${a}-${b}`).join(','));
		document.querySelectorAll('div.l.selected').forEach(el => el.classList.remove('selected'))

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


	document.querySelectorAll('a.l').forEach(a => {
		a.addEventListener('click', e => {
			e.preventDefault()
			const line = e.currentTarget.closest('div')
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
})
