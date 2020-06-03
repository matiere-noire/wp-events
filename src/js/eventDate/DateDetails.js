import { dateFormat, timeFormat } from '../utils'
const { useState } = wp.element
const { __, sprintf } = wp.i18n
const { PanelRow, Modal, Button, Card, CardHeader, CardBody, CardFooter, Icon } = wp.components
import { chevronUp, chevronDown } from '@wordpress/icons'

const DateDetails = ({ date, editDate, deleteDate, allPlaces }) => {
  const [confirmOpen, setConfirmOpen] = useState(false)
  const [bodyOpen, setBodyOpen] = useState(true)

  const place = date.place && allPlaces && allPlaces.find( p => p.id === parseInt( date.place, 10 ))
  return (
    <PanelRow>
      <Card size="extraSmall" style={{ width: '100%' }}>
        <CardHeader style={{ padding: 0 }}>
          <Button onClick={() => setBodyOpen(!bodyOpen)} style={{ width: '100%', display: 'flex', justifyContent: 'space-between' }}>
            {date.startDate.toLocaleDateString()}
            <Icon className="components-panel__arrow" icon={bodyOpen ? chevronUp : chevronDown} />
          </Button>
        </CardHeader>
        {bodyOpen && (
          <>
            <CardBody>
              <PanelRow>
                <span>{__('Start')}</span>
                <span>{dateFormat(date.startDate)}</span>
              </PanelRow>

              <PanelRow>
                <span>{__('End')}</span>
                <span>{dateFormat(date.endDate)}</span>
              </PanelRow>

              <PanelRow>
                <span>{__('Place')}</span>
                <span>{place && place.name}</span>
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
          </>
        )}
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
