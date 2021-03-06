import classes from './TrainLeg.scss'
import LegIcon from 'components/controls/leg-icon/LegIcon'
import { LocationsHelper, DateTimeHelper } from 'utils'

export default class TrainLeg extends React.Component {
  constructor() {
    super();
  }

  static propTypes = {
    callingPoints: React.PropTypes.array.isRequired,
    locations: React.PropTypes.array.isRequired
  };

  renderCallingPoint(callingPoint, index) {
    return (
      <li className={classes.callingPoint} key={index} >
        <div className={classes.time} >{DateTimeHelper.parseTime(callingPoint.time)} </div>
        <div className={classes.station} >
          {LocationsHelper.getNameByCode(this.props.locations, callingPoint.station)}
        </div>
      </li>
    )
  }

  render() {
    const { className, callingPoints, ...other } = this.props;

    return (
      <section className={classnames(classes.trainLeg, className)}>
        <LegIcon mode="train" />
        <ul className={classes.callingPoints}>
          {_.map(callingPoints, this.renderCallingPoint, this)}
        </ul>
      </section>
    )
  }
}
