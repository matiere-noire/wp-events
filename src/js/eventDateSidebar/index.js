const { registerPlugin } = wp.plugins
const { subscribe } = wp.data

import DateSidebar from './DateSidebar'
import './store'

registerPlugin('wpe-event-sidebar', {
  icon: 'calendar',
  render: DateSidebar
})
