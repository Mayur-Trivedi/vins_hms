import * as types from '../mutation-types'
import _ from 'lodash'
import user from '../../api/users'
import moment from 'moment'

// initial state
const state = {
	'patientId':0,
  	'ipdId':0,
    'admitDatetime': '',
  	'patientData': [],
    'ipdData': [],
 }

 // getters
const getters = {
   getIpdID: state => {
      return state.ipdId
    },
  }
// actions
const actions = {
  SetPatientId ({commit},patientId) {
    commit(types.SET_PATIENT_ID, patientId)
  },
  SetIpdId ({commit},ipdId) {
    commit(types.SET_IPD_ID, ipdId)
  },
  SetPatientData ({commit},ipdId) {
    
    user.getpatientDetail(ipdId).then(
    (response) => {
      if(response.data.code == 200) {
        commit(types.SET_PATIENT_DATA, response.data.data);
        
      }
    },
    
    )
  },
  GetAllPatientName({commit}) {
        user.getAllPatientName().then(
    (response) => {
      if(response.data.code == 200) {
        commit(types.SET_IPD_DATA, response.data.data);
        
      }
    },
    
    )
  },
}

// mutations
const mutations = {
  [types.SET_IPD_ID] (state, ipdId) {
    state.ipdId = ipdId
  },
  [types.SET_PATIENT_ID] (state, patientId) {
      state.patientId = patientId
  },
  [types.SET_PATIENT_DATA] (state, patientData) {
    // console.log(patientData)
      state.patientData = patientData.patient_details;
      state.admitDatetime = patientData.admit_datetime;
      state.patientId = patientData.patient_details.id;
      state.ipdId = patientData.id;
  },
    [types.SET_IPD_DATA] (state, ipdData) {
    // console.log(patientData)
      state.ipdData = ipdData;
  },
  
}

export default {
  state,
  // getters,
  actions,
  mutations
}