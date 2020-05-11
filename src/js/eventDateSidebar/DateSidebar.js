const { Fragment, Component } = wp.element
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost
const { DateTimePicker, PanelBody, PanelRow, Popover, SelectControl } = wp.components
const { __experimentalGetSettings, dateI18n } = wp.date
const { compose } = wp.compose
const { withSelect, withDispatch } = wp.data
const { apiFetch } = wp

import uuid from 'uuid/v4'

import DateDetails from './DateDetails'
// import AddDateModal from './AddDateModal'

class DateSidebar extends Component {
  constructor(props) {
    super(props)

    this.state = {
      isStartPickerOpen: false,
      isEndPickerOpen: false,
      currentEditDate: {
        startDate: new Date(),
        endDate: new Date(),
        status: 'new',
        place: '',
        id: uuid()
      },
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

  toggleStartPicker() {
    this.setState({ isStartPickerOpen: !this.state.isStartPickerOpen })
  }

  toggleEndPicker() {
    this.setState({ isEndPickerOpen: !this.state.isEndPickerOpen })
  }

  changeCurrentEditStartDate(date) {
    const { currentEditDate } = this.state
    const start = new Date(date.date)
    this.setState({
      currentEditDate: {
        ...currentEditDate,
        startDate: start
      }
    })
  }

  changeCurrentEditEndDate(date) {
    const { currentEditDate } = this.state
    const end = new Date(date.date)
    this.setState({
      currentEditDate: {
        ...currentEditDate,
        endDate: end
      }
    })
  }

  changeCurrentEditPlace(p) {
    const { currentEditDate } = this.state
    this.setState({
      currentEditDate: {
        ...currentEditDate,
        place: p
      }
    })
  }

  // editDate(date) {
  //   const currentEditDate = {
  //     ...date,
  //     status: date.status === 'new' ? 'new' : 'edit'
  //   }
  //   this.setState({ dateOpen: true, currentEditDate })
  // }

  render() {
    const settings = __experimentalGetSettings()
    const { isStartPickerOpen, isEndPickerOpen, currentEditDate, allPlaces } = this.state
    const { dates, deleteDate } = this.props

    return (
      <Fragment>
        <PluginSidebarMoreMenuItem target="wpe_event_sidebar">Les dates</PluginSidebarMoreMenuItem>
        <PluginSidebar name="wpe_event_sidebar" title="Les dates" isPinnable={true}>
          <PanelBody title="Nouvelle date" icon="calendar" initialOpen={true}>
            <PanelRow>
              <h2 style={{ margin: 0 }}>Date de début</h2>
              <button className="components-button is-link">
                <date onClick={() => this.toggleStartPicker()}>{dateI18n(settings.formats.datetime, currentEditDate.startDate)}</date>
              </button>
              {isStartPickerOpen && (
                <Popover onClose={() => {}}>
                  <DateTimePicker currentDate={currentEditDate.startDate} onChange={date => this.changeCurrentEditStartDate({ date })} is12Hour={false} />
                </Popover>
              )}
            </PanelRow>
            <PanelRow>
              <h2 style={{ margin: 0 }}>Date de fin</h2>
              <button className="components-button is-link">
                <date onClick={() => this.toggleEndPicker()}>{dateI18n(settings.formats.datetime, currentEditDate.endDate)}</date>
              </button>
              {isEndPickerOpen && (
                <Popover onClose={() => {}}>
                  <DateTimePicker currentDate={currentEditDate.endDate} onChange={date => this.changeCurrentEditEndDate({ date })} is12Hour={false} />
                </Popover>
              )}
            </PanelRow>
            <PanelRow>
              <h2>Salle</h2>
              <SelectControl
                label=""
                value={currentEditDate.place ? currentEditDate.place : null}
                onChange={place => this.changeCurrentEditPlace(place)}
                options={[{ value: null, label: 'Choisissez une salle' }, ...allPlaces]}
              />
            </PanelRow>
          </PanelBody>
          {dates.map(mapDate => {
            return <DateDetails date={mapDate} editDate={date => this.editDate(date)} deleteDate={date => deleteDate(date)} allPlaces={allPlaces} />
          })}
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
