import { Notify, Loading } from 'quasar';
import { onRequest, deleteEntity } from 'boot/axios-call';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';

export const onDeleteEntity = (
  entity: string,
  optimusId: number,
  label: string
) => {
  const { entityQuery } = storeToRefs(useCommonStore());
  Notify.create({
    message: `Delete ${label}?`,
    type: 'negative',
    actions: [
      {
        label: 'No',
        color: 'white',
        handler: () => {
          /* ... */
        },
      },
      {
        label: 'Yes',
        color: 'yellow',
        handler: async () => {
          Loading.show();
          const result = await deleteEntity({
            entity: entity,
            optimus_id: optimusId,
            label: label
          });
          if (result === true) {
            onRequest(entityQuery.value, true);
          }
          Loading.hide();
        },
      },
    ],
  });
};
