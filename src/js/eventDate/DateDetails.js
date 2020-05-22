import { moreVertical } from '@wordpress/icons'
import { dateFormat, timeFormat } from '../utils'
const { Fragment, useState } = wp.element
const { __, sprintf } = wp.i18n
const { PanelRow, Modal, Button, DropdownMenu } = wp.components

const DateDetails = ({ date, editDate, deleteDate }) => {
  const [confirmOpen, setConfirmOpen] = useState(false)

  const sameDay = (d1, d2) => {
    return d1.getFullYear() === d2.getFullYear() && d1.getMonth() === d2.getMonth() && d1.getDate() === d2.getDate()
  }

  const eventDates = sameDay(date.startDate, date.endDate)
    ? sprintf(__('From %s to %s'), dateFormat(date.startDate), timeFormat(date.endDate))
    : sprintf(__('From %s to %s'), dateFormat(date.startDate), dateFormat(date.endDate))

  return (
    <Fragment>
      <PanelRow>
        <span>{eventDates}</span>
        <DropdownMenu
          label="Select a direction"
          icon={moreVertical}
          controls={[
            {
              title: __('Edit'),
              icon: 'edit',
              onClick: () => editDate(date)
            },
            {
              title: __('Delete'),
              icon: 'trash',
              onClick: () => setConfirmOpen(true)
            }
          ]}
        />
      </PanelRow>

      {confirmOpen && (
        <Modal title={__('Delete date')} onRequestClose={() => setConfirmOpen(false)}>
          <p>{sprintf(__('Are you sure you want to delete the date "%s"'), dateFormat(date.startDate))}</p>
          <Button isDefault onClick={() => setConfirmOpen(false)}>
            {__('Cancel')}
          </Button>
          <Button
            isPrimary
            onClick={() => {
              setConfirmOpen(false)
              deleteDate(date)
            }}
          >
            {__('Confirm delete date')}
          </Button>
        </Modal>
      )}
    </Fragment>
  )
}
export default DateDetails
