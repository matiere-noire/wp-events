const { PluginDocumentSettingPanel } = wp.editPost
const { registerPlugin } = wp.plugins
const { __ } = wp.i18n

import DatePanel from './DatePanel'
import './store'

const DateSettingPanel = () => (
  <PluginDocumentSettingPanel name="wp-events-dates" title={__('Dates')} icon="calendar">
    <DatePanel />
  </PluginDocumentSettingPanel>
)

registerPlugin('wp-events-dates', { render: DateSettingPanel })
