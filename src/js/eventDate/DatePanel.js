const { Fragment, useState } = wp.element
const { PanelRow, Button } = wp.components
const { compose } = wp.compose
const { withSelect, withDispatch } = wp.data
const { __ } = wp.i18n
const { apiFetch } = wp

import uuid from 'uuid/v4'

import DateDetails from './DateDetails'
import AddDateModal from './AddDateModal'

const DatePanel = ({ dates, deleteDate }) => {
  const [dateOpen, setDateOpen] = useState(false)
  const [currentEditDate, setCurrentEditDate] = useState({})

  const newDate = () => {
    setCurrentEditDate({
      startDate: new Date(),
      endDate: new Date(),
      status: 'new',
      place: '',
      id: uuid()
    })
    setDateOpen(true)
  }

  const editDate = date => {
    const currentEditDate = {
      ...date,
      status: date.status === 'new' ? 'new' : 'edit'
    }
    setCurrentEditDate(currentEditDate)
    setDateOpen(true)
  }

  return (
    <Fragment>
      <PanelRow>
        <Button isDefault onClick={() => newDate()}>
          {__('New date')}
        </Button>
      </PanelRow>
      {dates.map(mapDate => {
        return <DateDetails date={mapDate} editDate={date => editDate(date)} deleteDate={date => deleteDate(date)} allPlaces={[]} />
      })}
      {dateOpen && <AddDateModal closeModal={() => setDateOpen(false)} currentEditDate={currentEditDate} allPlaces={[]} />}
    </Fragment>
  )
}

export default compose([
  withSelect(select => {
    const eventId = select('core/editor').getCurrentPostId()
    const { getDates } = select('wpe/event-date')

    return {
      dates: eventId ? getDates(eventId) : []
    }
  }),
  withDispatch(dispatch => {
    return {
      deleteDate: date => dispatch('wpe/event-date').deleteDate(date)
    }
  })
])(DatePanel)
