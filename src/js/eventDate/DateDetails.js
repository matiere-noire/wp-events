import { dateFormat, timeFormat } from '../utils'
const { useState } = wp.element
const { __, sprintf } = wp.i18n
const { PanelRow, Modal, Button, Card, CardHeader, CardBody, CardFooter } = wp.components

const DateDetails = ({ date, editDate, deleteDate }) => {
  const [confirmOpen, setConfirmOpen] = useState(false)

  return (
    <PanelRow>
      <Card size="extraSmall" style={{ width: '100%' }}>
        <CardHeader>
          <strong>{date.startDate.toLocaleDateString()}</strong>
        </CardHeader>
        <CardBody>
          <PanelRow>
            <span>{__('Start')}</span>
            <span>{dateFormat(date.startDate)}</span>
          </PanelRow>
          <PanelRow>
            <span>{__('End')}</span>
            <span>{dateFormat(date.endDate)}</span>
          </PanelRow>
        </CardBody>
        <CardFooter style={{ display: 'flex', justifyContent: 'space-between' }}>
          <Button isSecondary onClick={() => editDate(date)}>
            {__('Edit')}
          </Button>
          <Button isDestructive onClick={() => setConfirmOpen(true)}>
            {__('Delete')}
          </Button>
        </CardFooter>
      </Card>
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
    </PanelRow>
  )
}
export default DateDetails
