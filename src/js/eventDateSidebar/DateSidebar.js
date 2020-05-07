const { Fragment, Component } = wp.element
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost
const { PanelBody, PanelRow, Button } = wp.components
const { compose } = wp.compose
const { withSelect, withDispatch } = wp.data
const { apiFetch } = wp

import uuid from 'uuid/v4'

import DateDetails from './DateDetails'
import AddDateModal from './AddDateModal'

class DateSidebar extends Component {
  constructor(props) {
    super(props)

    this.state = {
      dateOpen: false,
      currentEditDate: {},
      allPlaces: []
    }
  }

  componentDidMount() {
    apiFetch({ path: '/wp/v2/place?per_page=100' }).then(response => {
      // Remember places are hierarchical so...
      // we return all places which be part of a parent place

      // const getPlacesFiltered = response.filter(place => {
      //   if (place.parent !== 0) {
      //     return true
      //   }
      // })

      const getPlaces = response.map(place => {
        return { value: place.id, label: place.name }
      })

      this.setState({ allPlaces: getPlaces })
    })
  }

  newDate() {
    const currentEditDate = {
      startDate: new Date(),
      endDate: new Date(),
      status: 'new',
      place: '',
      id: uuid()
    }
    this.setState({ dateOpen: true, currentEditDate })
  }

  editDate(date) {
    const currentEditDate = {
      ...date,
      status: date.status === 'new' ? 'new' : 'edit'
    }
    this.setState({ dateOpen: true, currentEditDate })
  }

  closeModal() {
    console.log('pass close modal')
    this.setState({ dateOpen: false })
  }

  render() {
    const { dateOpen, currentEditDate, allPlaces } = this.state
    const { dates, deleteDate } = this.props

    return (
      <Fragment>
        <PluginSidebarMoreMenuItem target="wpe_event_sidebar">Les dates</PluginSidebarMoreMenuItem>
        <PluginSidebar name="wpe_event_sidebar" title="Les dates" isPinnable={true}>
          <PanelBody title="Les dates" icon="calendar" initialOpen={true}>
            <PanelRow>
              <Button isDefault onClick={() => this.newDate()}>
                Nouvelle date
              </Button>
            </PanelRow>
          </PanelBody>
          {dates.map(mapDate => {
            return <DateDetails date={mapDate} editDate={date => this.editDate(date)} deleteDate={date => deleteDate(date)} allPlaces={allPlaces} />
          })}
          {dateOpen && <AddDateModal closeModal={() => this.closeModal()} currentEditDate={currentEditDate} allPlaces={allPlaces} />}
        </PluginSidebar>
      </Fragment>
    )
  }
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
])(DateSidebar)
