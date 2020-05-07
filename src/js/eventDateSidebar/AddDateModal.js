const { Component } = wp.element
const { withDispatch, dispatch, withSelect } = wp.data
const { compose } = wp.compose
const { Button, DateTimePicker, Modal, SelectControl } = wp.components
const { apiFetch } = wp

/**
 * Expose a modal to add a date
 */
class AddDateModal extends Component {
  constructor(props) {
    super(props)

    this.state = {
      currentDate: { ...props.currentEditDate },
      apiError: null,
      apiFetching: false
    }
  }

  addPlace(p) {
    const { currentDate } = this.state
    const newEditDatePlace = {
      ...currentDate,
      place: p
    }
    this.setState({
      currentDate: newEditDatePlace
    })
  }

  changeCurrentEditStartDate(date) {
    const { currentDate } = this.state
    const start = new Date(date.date)
    this.setState({
      currentDate: {
        ...currentDate,
        startDate: start
      }
    })
  }

  changeCurrentEditEndDate(date) {
    const { currentDate } = this.state
    const end = new Date(date.date)
    this.setState({
      currentDate: {
        ...currentDate,
        endDate: end
      }
    })
  }

  prepareDateForApi() {
    const { currentDate } = this.state
    const { eventId } = this.props

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

  saveDate() {
    const { currentDate } = this.state
    const { closeModal } = this.props

    const date = this.prepareDateForApi()

    // API call
    this.setState({ apiFetching: true })

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

        this.setState({ apiFetching: false })

        closeModal()
      })
      .catch(error => {
        this.setState({
          apiError: error.message,
          apiFetching: false
        })
      })
  }

  render() {
    const { currentDate, apiError, apiFetching } = this.state
    const { closeModal, allPlaces } = this.props

    return (
      <Modal title="Détails de la date" onRequestClose={() => closeModal()}>
        {apiError && <div className="error-message">{apiError}</div>}

        <div style={{ display: 'flex', borderBottom: '1px dotted #000', paddingBottom: '1em', marginBottom: '2em' }}>
          <div>
            <h2>Date de début</h2>
            <DateTimePicker currentDate={currentDate.startDate} onChange={date => this.changeCurrentEditStartDate({ date })} is12Hour={false} />
          </div>

          <div>
            <h2>Date de fin</h2>
            <DateTimePicker currentDate={currentDate.endDate} onChange={date => this.changeCurrentEditEndDate({ date })} is12Hour={false} />
          </div>
        </div>

        <div style={{ borderBottom: '1px dotted #000', paddingBottom: '1em', marginBottom: '2em' }}>
          <h2>Salle</h2>
          <SelectControl
            label=""
            value={currentDate.place ? currentDate.place : null}
            onChange={place => this.addPlace(place)}
            options={[{ value: null, label: 'Choisissez une salle test' }, ...allPlaces]}
          />
        </div>

        <Button
          isPrimary
          onClick={() => {
            this.saveDate()
          }}
          style={{ marginTop: '2em' }}
          isBusy={apiFetching}
          disabled={apiFetching}
        >
          Enregistrer la date
        </Button>
      </Modal>
    )
  }
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
