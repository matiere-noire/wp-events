const { useState } = wp.element
const { withDispatch, dispatch, withSelect } = wp.data
const { compose } = wp.compose
const { Button, DateTimePicker, Modal, SelectControl, Dropdown, PanelRow } = wp.components
const { apiFetch } = wp
const { __ } = wp.i18n

/**
 * Expose a modal to add a date
 */
const AddDateModal = ({ currentEditDate, eventId, closeModal, allPlaces }) => {
  const [currentDate, setCurrentDate] = useState(currentEditDate)
  const [apiError, setApiError] = useState(null)
  const [apiFetching, setApiFetching] = useState(false)

  const addPlace = p => {
    setCurrentDate({
      ...currentDate,
      place: p
    })
  }

  const changeCurrentEditStartDate = date => {
    const start = new Date(date.date)
    const newDate = { ...currentDate }
    if (start > currentDate.endDate) {
      newDate.endDate = start
    }
    newDate.startDate = start
    setCurrentDate(newDate)
  }

  const changeCurrentEditEndDate = date => {
    const end = new Date(date.date)
    const newDate = { ...currentDate }
    if (end < currentDate.startDate) {
      newDate.startDate = end
    }
    newDate.endDate = end
    setCurrentDate(newDate)
  }

  const prepareDateForApi = () => {
    let saveDate = null
    switch (currentDate.status) {
      case 'new':
        saveDate = {
          date_start: currentDate.startDate,
          date_end: currentDate.endDate,
          event_id: eventId,
          place_id: currentDate.place
        }
        break
      case 'edit':
        saveDate = {
          date_start: currentDate.startDate,
          date_end: currentDate.endDate,
          event_id: eventId,
          place_id: currentDate.place,
          id: currentDate.id
        }
        break
      case 'saved':
        break
    }
    return saveDate
  }

  const saveDate = () => {
    const date = prepareDateForApi()

    // API call
    setApiFetching(true)

    // If date object contain if so we launch an update else we add new date
    apiFetch({
      path: `/wpe/v1/dates${date.id ? '/' + date.id : ''}`,
      method: date.id ? 'PUT' : 'POST',
      data: date
    })
      .then(response => {
        // We add data to store and close the modal
        let dateStore = { ...currentDate, status: 'saved' }
        const dateId = +response

        if (!isNaN(dateId)) {
          dateStore.id = dateId
        }

        dispatch('wpe/event-date').addDate(dateStore)

        setApiFetching(false)
        closeModal()
      })
      .catch(error => {
        setApiError(error.message)
        setApiFetching(false)
      })
  }

  return (
    <Modal title={__('Date details')} onRequestClose={() => closeModal()} shouldCloseOnClickOutside={false}>
      {apiError && <div className="error-message">{apiError}</div>}

      <PanelRow>
        <span>{__('Start date')}</span>
        <Dropdown
          position="bottom right"
          renderToggle={({ isOpen, onToggle }) => (
            <Button isLink onClick={onToggle} aria-expanded={isOpen}>
              {`${currentDate.startDate.toLocaleDateString(undefined, { hour: '2-digit', minute: '2-digit' })}`}
            </Button>
          )}
          renderContent={() => <DateTimePicker currentDate={currentDate.startDate} onChange={date => changeCurrentEditStartDate({ date })} is12Hour={false} />}
        />
      </PanelRow>
      <PanelRow>
        <span>{__('End date')}</span>
        <Dropdown
          position="bottom right"
          renderToggle={({ isOpen, onToggle }) => (
            <Button isLink onClick={onToggle} aria-expanded={isOpen}>
              {`${currentDate.endDate.toLocaleDateString(undefined, { hour: '2-digit', minute: '2-digit' })}`}
            </Button>
          )}
          renderContent={() => <DateTimePicker currentDate={currentDate.endDate} onChange={date => changeCurrentEditEndDate({ date })} is12Hour={false} />}
        />
      </PanelRow>

      {/*<div style={{ borderBottom: '1px dotted #000', paddingBottom: '1em', marginBottom: '2em' }}>*/}
      {/*  <h2>Salle</h2>*/}
      {/*  <SelectControl*/}
      {/*    label=""*/}
      {/*    value={currentDate.place ? currentDate.place : null}*/}
      {/*    onChange={place => addPlace(place)}*/}
      {/*    options={[{ value: null, label: 'Choisissez une salle test' }, ...allPlaces]}*/}
      {/*  />*/}
      {/*</div>*/}

      <Button
        isPrimary
        onClick={() => {
          saveDate()
        }}
        style={{ marginTop: '2em' }}
        isBusy={apiFetching}
        disabled={apiFetching}
      >
        {__('Save the date')}
      </Button>
    </Modal>
  )
}

export default compose([
  withSelect(select => {
    return {
      eventId: select('core/editor').getCurrentPostId()
    }
  }),
  withDispatch(dispatch => {
    return {
      addDate: date => dispatch('wpe/event-date').addDate(date)
    }
  })
])(AddDateModal)
