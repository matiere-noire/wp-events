const { Fragment, useState } = wp.element
const { PanelRow, Button } = wp.components
const { compose } = wp.compose
const { withSelect, withDispatch } = wp.data
const { __ } = wp.i18n

import uuid from 'uuid/v4'
import AfterDateSlot from './AfterDateSlot'

wp.wpEvent = { AfterDateSlot }

import DateDetails from './DateDetails'
import AddDateModal from './AddDateModal'

const DatePanel = ({ dates, deleteDate, places }) => {
  const [dateOpen, setDateOpen] = useState(false)
  const [currentEditDate, setCurrentEditDate] = useState({})

  const newDate = () => {
    setCurrentEditDate({
      startDate: new Date(),
      endDate: new Date(),
      status: 'new',
      place: '',
      id: uuid(),
    })
    setDateOpen(true)
  }

  const editDate = (date) => {
    const currentEditDate = {
      ...date,
      status: date.status === 'new' ? 'new' : 'edit',
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
      {dates.map((mapDate) => {
        return (
          <DateDetails
            key={`date-${mapDate.id}`}
            date={mapDate}
            editDate={(date) => editDate(date)}
            deleteDate={(date) => deleteDate(date)}
            allPlaces={places}
          />
        )
      })}
      <AfterDateSlot.Slot />
      {dateOpen && <AddDateModal closeModal={() => setDateOpen(false)} currentEditDate={currentEditDate} allPlaces={places} />}
    </Fragment>
  )
}

export default compose([
  withSelect((select) => {
    const eventId = select('core/editor').getCurrentPostId()
    const { getDates } = select('wpe/event-date')

    const places = select('core').getEntityRecords('taxonomy', 'place', { per_page: 100 })

    return {
      dates: eventId ? getDates(eventId) : [],
      places,
    }
  }),
  withDispatch((dispatch) => {
    return {
      deleteDate: (date) => dispatch('wpe/event-date').deleteDate(date),
    }
  }),
])(DatePanel)
